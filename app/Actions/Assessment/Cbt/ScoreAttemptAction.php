<?php

namespace App\Actions\Assessment\Cbt;

use App\Enum\Assessment\ExamAttemptStatusEnum;
use App\Enum\Assessment\GradingRuleEnum;
use App\Models\Assessment\Exam\ExamAnswer;
use App\Models\Assessment\Exam\ExamAttempt;
use App\Models\Assessment\Exam\ExamSectionResult;
use App\Models\Assessment\Question\Question;
use App\Models\Assessment\Question\QuestionOption;
use Illuminate\Support\Facades\DB;

class ScoreAttemptAction
{
    public function handle(ExamAttempt $attempt): ExamAttempt
    {
        return DB::transaction(function () use ($attempt) {
            $attempt->loadMissing([
                'session.template.sections.questions.options',
                'answers',
            ]);

            $answers = $attempt->answers->keyBy('question_id');
            $sections = $attempt->session->template->sections;

            $totalScore = 0.0;
            $allPassed = true;
            $answersPayload = [];
            $resultsPayload = [];

            foreach ($sections as $section) {
                $sectionScore = 0.0;

                foreach ($section->questions as $question) {
                    $userAnswer = $answers->get($question->id);
                    $score = 0.0;

                    if ($userAnswer?->selected_option_id) {
                        $selectedOption = $question->options->firstWhere('id', $userAnswer->selected_option_id);
                        $score = $selectedOption ? $this->calculateScore($question, $selectedOption) : 0.0;

                        $answersPayload[] = [
                            'id' => $userAnswer->id,
                            'exam_attempt_id' => $attempt->id,
                            'question_id' => $question->id,
                            'selected_option_id' => $userAnswer->selected_option_id,
                            'score_earned' => $score,
                            'time_spent_seconds' => $userAnswer->time_spent_seconds ?? 0,
                        ];
                    }

                    $sectionScore += $score;
                }

                $passed = \is_null($section->passing_grade) || $sectionScore >= (float) $section->passing_grade;
                $allPassed = $allPassed && $passed;

                $resultsPayload[] = [
                    'exam_attempt_id' => $attempt->id,
                    'exam_section_id' => $section->id,
                    'score' => $sectionScore,
                    'is_passed' => $passed,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $totalScore += $sectionScore;
            }

            // Eksekusi Bulk Database Writes
            if ($answersPayload) {
                ExamAnswer::upsert($answersPayload, ['id'], ['score_earned']);
            }

            if ($resultsPayload) {
                ExamSectionResult::upsert(
                    $resultsPayload,
                    ['exam_attempt_id', 'exam_section_id'],
                    ['score', 'is_passed', 'updated_at']
                );
            }

            $attempt->update([
                'total_score' => $totalScore,
                'is_passed' => $allPassed,
                'status' => ExamAttemptStatusEnum::COMPLETED,
                'submitted_at' => now(),
            ]);

            return $attempt->fresh(['sectionResults', 'answers']);
        });
    }

    private function calculateScore(Question $question, QuestionOption $option): float
    {
        if ($question->grading_rule === GradingRuleEnum::TKP) {
            return (float) $option->weight_score;
        }

        if (! $option->is_correct) {
            return 0.0;
        }

        return (float) ($option->weight_score > 0 ? $option->weight_score : 5.0);
    }
}
