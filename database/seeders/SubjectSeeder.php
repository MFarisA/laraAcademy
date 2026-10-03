<?php

namespace Database\Seeders;

use App\Models\Academic\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Mata pelajaran dasar untuk App: tiga komponen CAT BKN (TWK/TIU/TKP)
     * ditambah dua mata pelajaran khas SNBT.
     *
     * @var list<array{name: string, code: string, description: string}>
     */
    private const SUBJECTS = [
        [
            'name' => 'Tes Wawasan Kebangsaan',
            'code' => 'TWK',
            'description' => 'Pengetahuan kebangsaan: Pancasila, UUD 1945, struktur negara, dan sejarah Indonesia.',
        ],
        [
            'name' => 'Tes Intelligensi Umum',
            'code' => 'TIU',
            'description' => 'Kemampuan penalaran verbal, numerik, analitis, dan problem solving.',
        ],
        [
            'name' => 'Tes Kemampuan Dasar',
            'code' => 'TKP',
            'description' => 'Soal deskriptif dengan skala nilai 1-5 per butir, tidak ada jawaban salah.',
        ],
        [
            'name' => 'Matematika',
            'code' => 'MAT',
            'description' => 'Logika, aljabar, geometri, peluang, dan analisis data untuk seleksi SNBT.',
        ],
        [
            'name' => 'Bahasa Indonesia',
            'code' => 'BINDO',
            'description' => 'Tata bahasa, pemahaman bacaan, dan penyuntingan teks.',
        ],
    ];

    /**
     * Seeder ini idempoten: aman dijalankan berulang karena memakai
     * `firstOrCreate` dengan `code` sebagai kunci unik.
     */
    public function run(): void
    {
        foreach (self::SUBJECTS as $subject) {
            Subject::firstOrCreate(
                ['code' => $subject['code']],
                $subject,
            );
        }
    }
}
