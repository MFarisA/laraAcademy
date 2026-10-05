<?php

namespace Database\Seeders;

/**
 * Bank soal contoh berbahasa Indonesia, dikelompokkan per kode mata pelajaran.
 *
 * Dipakai `QuestionSeeder` supaya data awal benar-benar bisa dibaca dan dinilai,
 * bukan teks Latin dari `fake()->sentence()`. Ini juga yang membuat pencarian
 * teks penuh dengan konfigurasi `indonesian` teruji oleh data nyata.
 *
 * Tiap entri mendeklarasikan `grading_rule` sendiri lewat kunci `rule`, jadi
 * `QuestionSeeder` tidak perlu menebak jumlah soal per aturan.
 *
 * @see QuestionSeeder
 */
final class QuestionBank
{
    /**
     * Skala jawaban untuk soal `TKP`: tidak ada opsi salah, skor 1 sampai 5.
     *
     * @var list<string>
     */
    private const SCALE = [
        'Sangat tidak sesuai',
        'Tidak sesuai',
        'Ragu-ragu',
        'Sesuai',
        'Sangat sesuai',
    ];

    /**
     * @var array<string, list<array{rule: string, text: string, options: list<string>}>>
     */
    public const SUBJECTS = [
        'TWK' => [
            [
                'rule' => 'STANDARD',
                'text' => 'Jumlah sila yang terkandung dalam Pancasila adalah ...',
                'options' => [
                    'Tiga',
                    'Lima',
                    'Lima belas',
                    'Sembilan',
                ],
            ],
            [
                'rule' => 'STANDARD',
                'text' => 'Lembaga negara yang berwenang menguji undang-undang terhadap UUD NRI 1945 adalah ...',
                'options' => [
                    'Mahkamah Agung',
                    'Mahkamah Konstitusi',
                    'Pengadilan Tinggi',
                    'Badan Pemeriksa Keuangan',
                ],
            ],
            [
                'rule' => 'STANDARD',
                'text' => 'Tokoh yang menyusun rumusan dasar negara dalam PPKI pada tanggal 1 Agustus 1945 adalah ...',
                'options' => [
                    'Ir. Soekarno',
                    'Mr. Achmad Soebardjo',
                    'Mohammad Yamin',
                    'Sutan Syahrir',
                ],
            ],
            [
                'rule' => 'STANDARD',
                'text' => 'Daerah khusus yang diatur secara khusus dalam UUD NRI 1945 adalah ...',
                'options' => [
                    'Daerah Istimewa Yogyakarta',
                    'Aceh, Papua, Yogyakarta, dan Jakarta',
                    'Jawa, Sumatra, Sulawesi, dan Kalimantan',
                    'Bali, Nusa Tenggara Barat, dan Nusa Tenggara Timur',
                ],
            ],
            [
                'rule' => 'STANDARD',
                'text' => 'Sistem pemerintahan yang berlaku di Indonesia adalah ...',
                'options' => [
                    'Parlamenter',
                    'Presidensial',
                    'Semi presidensial',
                    'Parsipatoris',
                ],
            ],
            [
                'rule' => 'STANDARD',
                'text' => 'Proklamasi kemerdekaan Republik Indonesia dibacakan pada tanggal ...',
                'options' => [
                    '17 Agustus 1945',
                    '17 November 1945',
                    '8 Oktober 1945',
                    '21 Juni 1945',
                ],
            ],
            [
                'rule' => 'TKP',
                'text' => 'Seberapa besar Anda setuju bahwa disiplin nasional perlu diperkuat oleh seluruh warga negara?',
                'options' => self::SCALE,
            ],
            [
                'rule' => 'TKP',
                'text' => 'Seberapa besar Anda setuju bahwa pengelolaan sampah harus menjadi tanggung jawab bersama, bukan hanya pemerintah daerah?',
                'options' => self::SCALE,
            ],
        ],

        'TIU' => [
            [
                'rule' => 'STANDARD',
                'text' => 'Lawanan dari kata demokratis adalah ...',
                'options' => ['Oligarki', 'Monarki', 'Aristokrasi', 'Birokrasi'],
            ],
            [
                'rule' => 'STANDARD',
                'text' => 'Jika 3x + 7 = 22, maka nilai x adalah ...',
                'options' => ['3', '4', '5', '6'],
            ],
            [
                'rule' => 'STANDARD',
                'text' => 'Deret 3, 6, 12, 24, 48, ... memiliki suku berikutnya ...',
                'options' => ['72', '84', '96', '100'],
            ],
            [
                'rule' => 'STANDARD',
                'text' => 'Lima orang menyelesaikan suatu pekerjaan dalam 12 hari. Berapa hari tiga orang membutuhkan untuk pekerjaan yang sama?',
                'options' => ['8', '15', '20', '36'],
            ],
            [
                'rule' => 'STANDARD',
                'text' => 'Jika 60% dari suatu bilangan adalah 90, maka bilangan itu adalah ...',
                'options' => ['120', '135', '150', '180'],
            ],
            [
                'rule' => 'STANDARD',
                'text' => 'Urutan yang benar secara kamus dari kata bola, buku, dan bulan adalah ...',
                'options' => [
                    'bola, buku, bulan',
                    'buku, bola, bulan',
                    'bola, bulan, buku',
                    'buku, bulan, bola',
                ],
            ],
            [
                'rule' => 'TKP',
                'text' => 'Seberapa besar Anda yakin bahwa Anda dapat bekerja sama dengan rekan kerja yang memiliki kebiasaan berbeda?',
                'options' => self::SCALE,
            ],
            [
                'rule' => 'TKP',
                'text' => 'Seberapa besar Anda menganggap bahwa peserta pelatihan perlu menyelesaikan tugas akhir secara mandiri?',
                'options' => self::SCALE,
            ],
        ],

        'TKP' => [
            [
                'rule' => 'TKP',
                'text' => 'Saya setuju bahwa setiap peserta pelatihan harus mengikuti ujian akhir.',
                'options' => self::SCALE,
            ],
            [
                'rule' => 'TKP',
                'text' => 'Saya menganggap bahwa nilai yang diberikan perlu diperiksa lagi.',
                'options' => self::SCALE,
            ],
            [
                'rule' => 'TKP',
                'text' => 'Saya yakin bahwa saya dapat bekerja sama dengan orang yang berbeda.',
                'options' => self::SCALE,
            ],
            [
                'rule' => 'TKP',
                'text' => 'Saya menganggap bahwa jadwal pelatihan sebaiknya diumumkan lebih awal.',
                'options' => self::SCALE,
            ],
            [
                'rule' => 'TKP',
                'text' => 'Saya setuju bahwa hasil pelatihan perlu dinilai oleh atasan langsung.',
                'options' => self::SCALE,
            ],
            [
                'rule' => 'TKP',
                'text' => 'Saya yakin bahwa saya dapat mengikuti seluruh rangkaian sesi pelatihan.',
                'options' => self::SCALE,
            ],
            [
                'rule' => 'TKP',
                'text' => 'Saya menganggap bahwa modul pelatihan sebaiknya tersedia dalam bentuk digital.',
                'options' => self::SCALE,
            ],
            [
                'rule' => 'TKP',
                'text' => 'Saya setuju bahwa peserta pelatihan perlu mendapat sertifikat setelah selesai.',
                'options' => self::SCALE,
            ],
        ],

        'MAT' => [
            [
                'rule' => 'STANDARD',
                'text' => 'Hasil dari 15 x 8 : 4 + 36 - 10 adalah ...',
                'options' => ['46', '52', '56', '60'],
            ],
            [
                'rule' => 'STANDARD',
                'text' => 'Nilai x yang memenuhi persamaan 2x + 5 = 17 adalah ...',
                'options' => ['4', '5', '6', '7'],
            ],
            [
                'rule' => 'STANDARD',
                'text' => 'Keliling lingkaran dengan jari-jari 14 cm dan pi = 22/7 adalah ...',
                'options' => ['44 cm', '62 cm', '88 cm', '154 cm'],
            ],
            [
                'rule' => 'STANDARD',
                'text' => 'Dari 20 siswa, 12 suka peta, 8 suka matematika, dan 3 suka keduanya. Berapa siswa yang tidak suka keduanya?',
                'options' => ['0', '1', '3', '5'],
            ],
            [
                'rule' => 'STANDARD',
                'text' => 'Jika f(x) = 2x + 1 dan g(x) = x^2, maka f(g(3)) adalah ...',
                'options' => ['7', '10', '19', '25'],
            ],
            [
                'rule' => 'STANDARD',
                'text' => 'Peluang muncul angka pada lemparan satu koin adalah ...',
                'options' => ['1/4', '1/3', '1/2', '1'],
            ],
            [
                'rule' => 'TKP',
                'text' => 'Saya menganggap bahwa soal cerita perlu ada dalam ujian matematika.',
                'options' => self::SCALE,
            ],
            [
                'rule' => 'TKP',
                'text' => 'Saya yakin bahwa latihan soal setiap hari dapat meningkatkan hasil belajar.',
                'options' => self::SCALE,
            ],
        ],

        'BINDO' => [
            [
                'rule' => 'STANDARD',
                'text' => 'Gabungan kata ber dan lari ditulis dengan benar ...',
                'options' => ['berlari', 'berlaru', 'belari', 'barlari'],
            ],
            [
                'rule' => 'STANDARD',
                'text' => 'Kalimat di bawah ini yang termasuk kalimat efektif ...',
                'options' => ['Karena hujan deras, maka rapat dibatalkan.', 'Karena hujan deras, rapat dibatalkan.', 'Hujan deras, maka rapat dibatalkan.', 'Karena hujan deras sehingga rapat dibatalkan.'],
            ],
            [
                'rule' => 'STANDARD',
                'text' => 'Kata prasejarah ditulis dengan benar ...',
                'options' => ['prasejarah', 'pra sejarah', 'prase-jarah', 'prae sejarah'],
            ],
            [
                'rule' => 'STANDARD',
                'text' => 'Jenis kalimat yang tidak memiliki subjek disebut ...',
                'options' => ['Kalimat nominal', 'Kalimat verbal', 'Kalimat elipsis', 'Kalimat minor'],
            ],
            [
                'rule' => 'STANDARD',
                'text' => 'Sinonim dari kata mandiri adalah ...',
                'options' => ['swadaya', 'terikat', 'bergantung', 'tunakarya'],
            ],
            [
                'rule' => 'STANDARD',
                'text' => 'Tanda baca yang tepat sebelum kata tetapi di dalam kalimat ...',
                'options' => ['Koma', 'Titik koma', 'Titik dua', 'Tanda hubung'],
            ],
            [
                'rule' => 'TKP',
                'text' => 'Saya menganggap bahwa membaca bacaan tulis secara berkala itu penting.',
                'options' => self::SCALE,
            ],
            [
                'rule' => 'TKP',
                'text' => 'Saya setuju bahwa media sosial perlu dipakai secara bertanggung jawab.',
                'options' => self::SCALE,
            ],
        ],
    ];
}
