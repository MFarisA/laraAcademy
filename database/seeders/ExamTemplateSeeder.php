<?php

namespace Database\Seeders;

use App\Models\Academic\Program;
use App\Models\Academic\Subject;
use App\Models\Assessment\Exam\ExamSection;
use App\Models\Assessment\Exam\ExamTemplate;
use App\Models\Assessment\Question\Question;
use Illuminate\Database\Seeder;

class ExamTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cpnsProgram = Program::where('name', 'like', '%CPNS%')->first() ?? Program::first();
        $snbtProgram = Program::where('name', 'like', '%SNBT%')->first() ?? Program::latest()->first();

        // 1. Template SKD CPNS
        if ($cpnsProgram) {
            $skdTemplate = ExamTemplate::firstOrCreate(
                [
                    'program_id' => $cpnsProgram->id,
                    'title' => 'Simulasi Akbar CAT SKD CPNS & Kedinasan 2026',
                ],
                [
                    'type' => 'skd',
                    'total_duration_minutes' => 100,
                ]
            );

            $twk = Subject::where('code', 'TWK')->first();
            $tiu = Subject::where('code', 'TIU')->first();
            $tkp = Subject::where('code', 'TKP')->first();

            $sectionsConfig = [
                ['subject' => $twk, 'title' => 'Tes Wawasan Kebangsaan (TWK)', 'grade' => 65.00, 'duration' => 30, 'order' => 1],
                ['subject' => $tiu, 'title' => 'Tes Inteligensi Umum (TIU)', 'grade' => 80.00, 'duration' => 35, 'order' => 2],
                ['subject' => $tkp, 'title' => 'Tes Karakteristik Pribadi (TKP)', 'grade' => 156.00, 'duration' => 35, 'order' => 3],
            ];

            foreach ($sectionsConfig as $cfg) {
                if (! $cfg['subject']) {
                    continue;
                }

                $section = ExamSection::firstOrCreate(
                    [
                        'exam_template_id' => $skdTemplate->id,
                        'subject_id' => $cfg['subject']->id,
                    ],
                    [
                        'title' => $cfg['title'],
                        'passing_grade' => $cfg['grade'],
                        'duration_minutes' => $cfg['duration'],
                        'order_index' => $cfg['order'],
                    ]
                );

                // Hubungkan soal-soal yang ada di database ke section
                $questions = Question::where('subject_id', $cfg['subject']->id)->take(30)->get();
                $syncData = [];
                foreach ($questions as $qIndex => $q) {
                    $syncData[$q->id] = ['order_index' => $qIndex + 1];
                }
                if (! empty($syncData)) {
                    $section->questions()->sync($syncData);
                }
            }
        }

        // 2. Template SNBT
        if ($snbtProgram) {
            $snbtTemplate = ExamTemplate::firstOrCreate(
                [
                    'program_id' => $snbtProgram->id,
                    'title' => 'Try Out Intensif SNBT UTBK Potensi Skolastik 2026',
                ],
                [
                    'type' => 'snbt',
                    'total_duration_minutes' => 90,
                ]
            );

            $mat = Subject::where('code', 'MAT')->first();
            $bindo = Subject::where('code', 'BINDO')->first();

            $snbtSections = [
                ['subject' => $mat, 'title' => 'Penalaran Matematika', 'grade' => 70.00, 'duration' => 45, 'order' => 1],
                ['subject' => $bindo, 'title' => 'Literasi Bahasa Indonesia', 'grade' => 70.00, 'duration' => 45, 'order' => 2],
            ];

            foreach ($snbtSections as $cfg) {
                if (! $cfg['subject']) {
                    continue;
                }

                $section = ExamSection::firstOrCreate(
                    [
                        'exam_template_id' => $snbtTemplate->id,
                        'subject_id' => $cfg['subject']->id,
                    ],
                    [
                        'title' => $cfg['title'],
                        'passing_grade' => $cfg['grade'],
                        'duration_minutes' => $cfg['duration'],
                        'order_index' => $cfg['order'],
                    ]
                );

                $questions = Question::where('subject_id', $cfg['subject']->id)->take(25)->get();
                $syncData = [];
                foreach ($questions as $qIndex => $q) {
                    $syncData[$q->id] = ['order_index' => $qIndex + 1];
                }
                if (! empty($syncData)) {
                    $section->questions()->sync($syncData);
                }
            }
        }
    }
}
