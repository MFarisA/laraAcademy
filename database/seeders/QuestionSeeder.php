<?php

namespace Database\Seeders;

use App\Enum\Assessment\GradingRuleEnum;
use App\Models\Academic\Subject;
use App\Models\Assessment\Question\Question;
use App\Models\Assessment\Question\QuestionOption;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Label opsi bersifat posisional per question, jadi tidak bisa di-generate
     * acak oleh factory.
     *
     * @var list<string>
     */
    private const OPTION_LABELS = ['A', 'B', 'C', 'D', 'E'];

    /**
     * `difficulty_level` belum punya enum di kode, jadi nilainya bebas.
     *
     * @var list<string>
     */
    private const DIFFICULTIES = ['easy', 'medium', 'hard'];

    private const STANDARD_PER_SUBJECT = 6;

    private const TKP_PER_SUBJECT = 2;

    /**
     * Seeder ini idempoten: berhenti sendiri kalau bank soal sudah terisi,
     * sehingga `db:seed` tidak menumpuk soal diduplikasi.
     */
    public function run(): void
    {
        if (Question::withTrashed()->exists()) {
            return;
        }

        $subjects = Subject::all();

        if ($subjects->isEmpty()) {
            return;
        }

        foreach ($subjects->values() as $index => $subject) {
            $this->seedRule($subject, GradingRuleEnum::STANDARD, self::STANDARD_PER_SUBJECT, $index);
            $this->seedRule($subject, GradingRuleEnum::TKP, self::TKP_PER_SUBJECT, $index + 1);
        }
    }

    /**
     * @param  int  $count  jumlah soal untuk rule ini
     * @param  int  $difficultyIndex  untuk menggeser difficulty antar subject
     */
    private function seedRule(Subject $subject, GradingRuleEnum $rule, int $count, int $difficultyIndex): void
    {
        $questions = Question::factory()
            ->for($subject)
            ->count($count)
            ->difficulty(self::DIFFICULTIES[$difficultyIndex % count(self::DIFFICULTIES)])
            ->when(
                $rule === GradingRuleEnum::TKP,
                fn ($factory) => $factory->tkp(),
            )
            ->create();

        $questions->each(fn (Question $question) => $this->createOptions($question, $rule));
    }

    /**
     * Opsi harus sesuai aturan `grading_rule`, kalau tidak datanya akan
     * ditolak oleh validasi aplikasi sendiri saat soal disimpan lewat UI.
     * Logikanya mengikuti `StoreQuestionsAction::transformOption()`.
     */
    private function createOptions(Question $question, GradingRuleEnum $rule): void
    {
        $isTkp = $rule === GradingRuleEnum::TKP;
        $total = $isTkp ? count(self::OPTION_LABELS) : 4;
        $correctIndex = $isTkp ? null : fake()->numberBetween(0, $total - 1);

        foreach (range(0, $total - 1) as $index) {
            QuestionOption::factory()->create([
                'question_id' => $question->id,
                'option_label' => self::OPTION_LABELS[$index],
                'is_correct' => ! $isTkp && $index === $correctIndex,
                'weight_score' => $isTkp
                    ? fake()->numberBetween(1, 5)
                    : (int) ($index === $correctIndex),
            ]);
        }
    }
}
