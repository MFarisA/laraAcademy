<?php

namespace Database\Seeders;

use App\Models\Academic\Subject;
use App\Models\Learning\LearningMaterial;
use Illuminate\Database\Seeder;

class LearningMaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = Subject::all();

        $materialsBySubject = [
            'TWK' => [
                ['title' => 'Rangkuman UUD 1945 & Amandemen Lengkap', 'type' => 'pdf'],
                ['title' => 'Video Bedah Soal Pancasila & Pilar Kebangsaan', 'type' => 'video'],
                ['title' => 'Catatan Sejarah Nasional & Tokoh Bangsa', 'type' => 'pdf'],
            ],
            'TIU' => [
                ['title' => 'Modul Trik Cepat Berhitung Aljabar & Deret Angka', 'type' => 'pdf'],
                ['title' => 'Strategi Menjawab Silogisme & Logika Posisi', 'type' => 'pdf'],
                ['title' => 'Video Pembahasan Soal Figural & Spasial CAT BKN', 'type' => 'video'],
            ],
            'TKP' => [
                ['title' => 'Panduan Karakteristik Jawaban Skor 5 TKP Anti-Gagal', 'type' => 'pdf'],
                ['title' => 'Studi Kasus Pelayanan Publik & Jejaring Kerja', 'type' => 'pdf'],
            ],
            'MAT' => [
                ['title' => 'Formula Cepat Geometri & Trigonometri SNBT', 'type' => 'pdf'],
                ['title' => 'Video Kupas Tuntas Peluang & Kombinatorika', 'type' => 'video'],
            ],
            'BINDO' => [
                ['title' => 'Kaidah Ejaan Bahasa Indonesia (PUEBI) Esensial', 'type' => 'pdf'],
                ['title' => 'Latihan Pemahaman Paragraf & Ide Pokok', 'type' => 'pdf'],
            ],
        ];

        foreach ($subjects as $subject) {
            $items = $materialsBySubject[$subject->code] ?? [
                ['title' => "Modul Pengantar {$subject->name}", 'type' => 'pdf'],
                ['title' => "Video Pembahasan {$subject->name}", 'type' => 'video'],
            ];

            foreach ($items as $item) {
                LearningMaterial::firstOrCreate(
                    [
                        'subject_id' => $subject->id,
                        'title' => $item['title'],
                    ],
                    [
                        'type' => $item['type'],
                        'file_url' => $item['type'] === 'video'
                            ? 'https://youtube.com/watch?v=sample-video'
                            : "https://storage.laraacademy.id/materials/{$subject->code}_sample.pdf",
                        'is_downloadable' => true,
                    ]
                );
            }
        }
    }
}
