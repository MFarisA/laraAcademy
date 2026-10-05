<?php

namespace Database\Seeders;

use App\Enum\Assessment\DifficultyLevelEnum;
use App\Enum\Assessment\GradingRuleEnum;
use App\Models\Academic\Subject;
use App\Models\Assessment\Question\Question;
use App\Models\Assessment\Question\QuestionOption;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

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
     * @var list<DifficultyLevelEnum>
     */
    private const DIFFICULTIES = [
        DifficultyLevelEnum::EASY,
        DifficultyLevelEnum::MEDIUM,
        DifficultyLevelEnum::HARD,
    ];

    /**
     * Seeder ini idempoten: berhenti sendiri kalau bank soal sudah terisi,
     * sehingga `db:seed` tidak menumpuk soal diduplikasi.
     *
     * Pembungkus transaction penting: kalau `QuestionBank` tidak lengkap,
     * `run()` melempar exception di tengah jalan. Tanpa transaction, sebagian
     * soal sudah terlanjur tersimpan dan guard di atas membuat seed berikutnya
     * menganggap bank soal sudah penuh.
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

        DB::transaction(function () use ($subjects): void {
            foreach ($subjects->values() as $index => $subject) {
                $groups = $this->groupByRule($subject);

                foreach ($groups as $ruleName => $bank) {
                    $rule = GradingRuleEnum::from($ruleName);

                    $this->seedRule(
                        $subject,
                        $rule,
                        $bank,
                        $index + array_search($rule, GradingRuleEnum::cases(), true),
                    );
                }
            }
        });
    }

    /**
     * Bank soal dikelompokkan berdasarkan `grading_rule` yang dideklarasikan
     * tiap entri, supaya jumlah dan jenis opsi selalu konsisten dengan
     * validasinya.
     *
     * @return array<string, list<array{rule: string, text: string, options: list<string>}>>
     */
    private function groupByRule(Subject $subject): array
    {
        $bank = QuestionBank::SUBJECTS[$subject->code] ?? [];

        if ($bank === []) {
            throw new RuntimeException(sprintf(
                'QuestionBank tidak punya soal untuk subject "%s".',
                (string) $subject->code,
            ));
        }

        $groups = [];

        foreach ($bank as $entry) {
            $groups[$entry['rule']][] = $entry;
        }

        return $groups;
    }

    /**
     * @param  list<array{rule: string, text: string, options: list<string>}>  $bank
     * @param  int  $difficultyIndex  untuk menggeser difficulty antar kelompok
     */
    private function seedRule(Subject $subject, GradingRuleEnum $rule, array $bank, int $difficultyIndex): void
    {
        $count = count($bank);

        $questions = Question::factory()
            ->for($subject)
            ->count($count)
            ->difficulty(self::DIFFICULTIES[$difficultyIndex % count(self::DIFFICULTIES)])
            ->when(
                $rule === GradingRuleEnum::TKP,
                fn ($factory) => $factory->tkp(),
            )
            ->sequence(fn (Sequence $sequence) => [
                'question_text' => $bank[$sequence->index]['text'],
            ])
            ->create();

        $questions->each(
            fn (Question $question, int $index) => $this->createOptions(
                $question,
                $rule,
                $bank[$index]['options'],
            ),
        );
    }

    /**
     * Opsi harus sesuai aturan `grading_rule`, kalau tidak datanya akan
     * ditolak oleh validasi aplikasi sendiri saat soal disimpan lewat UI.
     * Logikanya mengikuti `StoreQuestionsAction::transformOption()`.
     *
     * @param  list<string>  $optionTexts  teks opsi dari bank soal
     */
    private function createOptions(Question $question, GradingRuleEnum $rule, array $optionTexts): void
    {
        $isTkp = $rule === GradingRuleEnum::TKP;
        $total = $isTkp ? count(self::OPTION_LABELS) : 4;
        $correctIndex = $isTkp ? null : fake()->numberBetween(0, $total - 1);

        if (count($optionTexts) !== $total) {
            throw new RuntimeException(sprintf(
                'QuestionBank soal "%s" punya %d opsi, seharusnya %d.',
                $question->question_text,
                count($optionTexts),
                $total,
            ));
        }

        foreach (range(0, $total - 1) as $index) {
            QuestionOption::factory()->create([
                'question_id' => $question->id,
                'option_label' => self::OPTION_LABELS[$index],
                'option_text' => $optionTexts[$index],
                'is_correct' => ! $isTkp && $index === $correctIndex,
                'weight_score' => $isTkp
                    ? fake()->numberBetween(1, 5)
                    : (int) ($index === $correctIndex),
            ]);
        }
    }
}
