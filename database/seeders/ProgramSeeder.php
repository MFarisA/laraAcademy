<?php

namespace Database\Seeders;

use App\Models\Academic\Program;
use App\Models\Academic\Subject;
use Illuminate\Database\Seeder;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $programs = [
            [
                'name' => 'Persiapan CPNS & Sekolah Kedinasan 2026',
                'description' => 'Program komprehensif bimbel intensif menghadapi Seleksi Kompetensi Dasar (SKD) CAT BKN dan Tes Intelegensi.',
                'subjects' => [
                    'TWK' => 65.00,
                    'TIU' => 80.00,
                    'TKP' => 156.00,
                ],
            ],
            [
                'name' => 'Intensif SNBT UTBK 2026',
                'description' => 'Bimbingan intensif persiapan Tes Potensi Skolastik (TPS) dan Tes Literasi UTBK Masuk PTN.',
                'subjects' => [
                    'MAT' => 70.00,
                    'BINDO' => 75.00,
                    'TIU' => 75.00,
                ],
            ],
            [
                'name' => 'Akademi Khusus Masuk STAN & IPDN',
                'description' => 'Spesialisasi persiapan masuk sekolah kedinasan favorit Kemenkeu dan Kemendagri dengan target kelulusan tinggi.',
                'subjects' => [
                    'TWK' => 70.00,
                    'TIU' => 85.00,
                    'TKP' => 160.00,
                ],
            ],
        ];

        foreach ($programs as $progData) {
            $subjectsMapping = $progData['subjects'];
            unset($progData['subjects']);

            $program = Program::firstOrCreate(['name' => $progData['name']], $progData);

            foreach ($subjectsMapping as $code => $minScore) {
                $subject = Subject::where('code', $code)->first();
                if ($subject) {
                    $program->subjects()->syncWithoutDetaching([
                        $subject->id => ['min_passing_score' => $minScore],
                    ]);
                }
            }
        }
    }
}
