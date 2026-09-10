# laraAcademy — Panduan Struktur & Arsitektur Domain

Dokumen ini menjelaskan rancangan arsitektur, domain bisnis, entitas data, relasi, serta hierarki konsep pada platform **laraAcademy**.

---

## 1. Ikhtisar Sistem

**laraAcademy** adalah platform gabungan antara **LMS Bimbel Multi-Cabang** (seperti *Ganesha Operation*) dan **CBT Ujian Online** (seperti *Ruangguru*), yang dikhususkan untuk persiapan:
* **Ujian Kedinasan** (CAT BKN: IPDN, STIS, STAN, Poltekip/Poltekim, dll.)
* **Seleksi CPNS / PPPK** (SKD: TWK, TIU, TKP)
* **SNBT / UTBK** (TPS & Literasi)

### Stack Teknologi
* **Framework:** Laravel 12 (PHP 8.2+)
* **Database:** PostgreSQL (memanfaatkan JSONB, Full-text Search, concurrency locks)
* **Autentikasi:** Laravel Fortify (2FA TOTP, Recovery Codes, WebAuthn Passkeys)
* **Otorisasi (RBAC):** Spatie Laravel Permission + Laravel Policy + Gate Bypass
* **Frontend:** Inertia.js v2 + React + TypeScript + Tailwind CSS

---

## 2. Struktur Direktori Proyek

```text
laraAcademy/
├── app/
│   ├── Actions/                # Single-action classes (Fortify & domain actions)
│   ├── Concerns/               # Reusable model traits (mis. BelongsToBranch)
│   ├── Enum/                   # Backed enums (Roles, Permissions, Statuses)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Academic/       # Program, Subject, Batch, Classroom
│   │   │   ├── Account/        # User management
│   │   │   ├── Assessment/     # Exam & Physical assessment
│   │   │   ├── Learning/       # Schedules, Attendance, Materials
│   │   │   └── Organization/   # Branch controller
│   │   ├── Middleware/         # EnsureBranchContext, Spatie aliases
│   │   ├── Requests/           # FormRequest validations per domain
│   │   └── Responses/          # Custom responses (mis. LoginResponse)
│   ├── Models/
│   │   ├── Academic/           # Program, Subject, Batch, Classroom, Enrollment
│   │   ├── Assessment/         # Question, Option, ExamTemplate, ExamSession, Attempt
│   │   ├── Learning/           # ClassSchedule, Attendance, LearningMaterial
│   │   └── Organization/       # Branch
│   └── Policies/               # Model authorization policies
├── database/
│   ├── factories/              # Model factories untuk testing
│   ├── migrations/             # 25+ tabel database
│   └── seeders/                # RolesAndPermissionsSeeder, DatabaseSeeder
└── tests/
    └── Feature/                # Pest feature test suite per domain
```

---

## 3. Penjelasan Domain & Entitas Inti

### 3.1 Domain Organisasi & Pengguna

* **`branches` (Cabang):**
  Unit kantor cabang fisik bimbel (mis. *Cabang Jakarta Selatan*, *Cabang Surabaya*).
  * Menjadi batasan isolasi data (*branch scoping*).
  * Dikelola penuh oleh `super-admin`.
* **`users` (Pengguna):**
  Entitas akun tunggal yang memiliki salah satu role sistem. Dapat diasosiasikan ke satu `branch_id` tertentu (opsional untuk Super Admin).

#### Matriks Role Pengguna
| Role | Kode Enum | Batas Akses & Tanggung Jawab |
| :--- | :--- | :--- |
| **Super Admin** | `super-admin` | Akses penuh tanpa batas ke semua cabang, konfigurasi master, program, dan soal. |
| **Admin Cabang** | `admin-cabang` | Mengelola operasional terbatas pada cabangnya sendiri (ruang kelas, pendaftaran siswa, presensi). |
| **Instruktur** | `instructor` | Mengajar, melihat jadwal mengajarnya, mengisi presensi kelas, mengunggah materi, dan menilai tes fisik. |
| **Siswa** | `student` | Mengikuti kelas, melihat materi, mengerjakan ujian CBT, dan melihat hasil/rekap nilai. |
| **Evaluator** | `evaluator` | Menilai tes kesamaptaan fisik siswa kedinasan. |

---

### 3.2 Domain Akademik (Hierarki Kelas)

Hierarki akademik mengatur bagaimana bimbingan belajar diselenggarakan:

```text
Program (Paket Pembelajaran)
   ├── Subjects (Mata Pelajaran yang Diujikan)
   │      └── via pivot: program_subjects (min_passing_score)
   │
   └── Batches (Gelombang / Periode Angkatan Waktu)
          │
          └── Classrooms (Ruang Kelas Fisik/Online per Cabang)
                 ├── Enrollments (Siswa Terdaftar di Kelas)
                 ├── Class Schedules (Jadwal Pertemuan & Instruktur)
                 │      └── Attendances (Presensi Siswa)
                 └── Learning Materials (Materi Modul / Video)
```

1. **`programs` (Program Bimbingan):**
   Tujuan atau paket belajar besar yang ditawarkan bimbel.
   * *Contoh:* `"Intensif Kedinasan IPDN 2026"`, `"Persiapan SKD CPNS 2026"`, `"Super Intensif SNBT UTBK"`.
2. **`subjects` (Mata Pelajaran / Bidang Uji):**
   Mata pelajaran spesifik yang berdiri sendiri dan dapat dipakai lintas program.
   * *Contoh:* `TWK` (Tes Wawasan Kebangsaan), `TIU` (Tes Intelegensia Umum), `TKP` (Tes Karakteristik Pribadi), `Penalaran Matematika`.
3. **`program_subjects` (Pivot Program ↔ Subject):**
   Menghubungkan subject mana saja yang masuk ke dalam suatu program beserta nilai ambang batas kelulusannya (`min_passing_score`).
   * *Contoh:* Di program "SKD CPNS", syarat kelulusannya adalah:
     * TWK: min 65
     * TIU: min 80
     * TKP: min 166
4. **`batches` (Angkatan / Periode Gelombang):**
   Pembagian waktu pelaksanaan suatu program bimbingan belajar.
   * *Contoh:* Di bawah program "SKD CPNS", ada `"Batch 1 - Reguler (Januari - Mei)"` dan `"Batch 2 - Kilat (Juni - Juli)"`.
   * Memiliki atribut tanggal mulai (`start_date`) dan tanggal selesai (`end_date`).
5. **`classrooms` (Ruang Kelas):**
   Kelas belajar nyata tempat kegiatan belajar mengajar berlangsung. Kelas ini merupakan persilangan antara **1 Batch** dan **1 Cabang**.
   * *Contoh:* `"Kelas Kedinasan Alpha - Cabang Bandung"`.
   * Memiliki batas kuota kursi (`capacity`).
6. **`classroom_enrollments` (Pendaftaran Siswa ke Kelas):**
   Pencatatan masuknya siswa ke suatu kelas.
   * Status: `active`, `finished`, `dropped`.
   * Memakai *atomic database lock* saat proses pendaftaran untuk mencegah perebutan kuota (*race condition*).
7. **`class_schedules` (Jadwal Pertemuan):**
   Sesi belajar tertentu di kelas (online via link meeting atau tatap muka di ruang fisik) yang diampu oleh instruktur.
8. **`attendances` (Presensi Siswa):**
   Pencatatan kehadiran per siswa di setiap jadwal (`present`, `absent`, `excused`, `sick`, `late`), diverifikasi oleh instruktur (`verified_at`).
9. **`learning_materials` (Materi Belajar):**
   Modul/file PDF/video yang ditautkan ke subject/program dan hanya bisa diunduh oleh siswa yang aktif terdaftar.

---

### 3.3 Domain Bank Soal & CBT Ujian (Core Engine)

Sistem ujian dirancang untuk meniru persis ujian CAT BKN dan UTBK SNBT:

```text
Bank Soal (Questions per Subject)
   └── Options (Pilihan Jawaban + Bobot / Kunci)
          │
          ▼
Exam Template (Blueprint / Format Tes)
   └── Exam Sections (Subtes: TWK, TIU, TKP)
          │
          ▼
Exam Sessions (Jadwal & Target Pelaksanaan)
   └── Exam Attempts (Lembar Pengerjaan Siswa)
          ├── Exam Answers (Jawaban Autosave per Soal)
          └── Exam Section Results (Skor per Subtes + Status Passing Grade)
```

1. **`questions` & `question_options` (Bank Soal):**
   Daftar butir soal yang dibuat per mata pelajaran (`subject_id`).
   * **`grading_rule` (Aturan Penilaian):**
     * **`Standard`**: Soal pilihan ganda biasa (1 atau lebih jawaban benar bernilai poin penuh, salah bernilai 0). Dipakai untuk TWK, TIU, Matematika.
     * **`Tkp`**: Semua opsi bernilai 1 s.d 5 (tidak ada jawaban salah). Khas tes TKP CPNS/Kedinasan.
2. **`exam_templates` (Template / Cetak Biru Ujian):**
   Format standar paket ujian dan total alokasi waktunya.
   * *Contoh:* `"Simulasi CAT SKD Nasional Kedinasan"` (Total 100 menit, 110 butir soal).
3. **`exam_sections` (Subtes di dalam Ujian):**
   Pembagian sesi di dalam template ujian.
   * *Contoh:*
     * Seksi 1: TWK (30 soal, passing grade 65, durasi fleksibel/terikat)
     * Seksi 2: TIU (35 soal, passing grade 80)
     * Seksi 3: TKP (45 soal, passing grade 166)
4. **`exam_sessions` (Sesi Ujian Nyata):**
   Pelaksanaan ujian aktual yang dibuka pada rentang waktu tertentu (`start_time` s/d `end_time`) dan diamankan dengan token akses unik.
   * Bisa ditargetkan ke kelas tertentu (`classroom_id`) atau terbuka nasional (`classroom_id = null`).
5. **`exam_attempts` (Lembar Kerja Ujian):**
   Satu sesi pengerjaan oleh siswa tertentu.
   * **Batas Waktu Server-Side:** Batas akhir submit dihitung mutlak di server (`started_at + durasi`) untuk mencegah kecurangan manipulasi jam lokal browser.
   * **Autosave (`exam_answers`):** Jawaban siswa disimpan secara berkala tiap detik/klik.
6. **`exam_section_results` & Scoring Engine:**
   Hasil rekap penilaian otomatis setelah ujian disubmit:
   * Menghitung nilai per seksi berdasarkan aturan `Standard` vs `Tkp`.
   * Mengevaluasi apakah siswa lolos *passing grade* di **semua** seksi wajib.
   * Menghitung **Ranking Nasional** dan **Ranking Cabang** (*leaderboard*).

---

### 3.4 Domain Penilaian Fisik / Kesamaptaan

* **`physical_assessments` & `physical_test_scores`:**
  Khusus seleksi kedinasan (IPDN, Poltekip, dll.) yang memerlukan tes fisik jasmani.
  * Metrik baku: Lari 12 menit, *Push-up* 1 menit, *Sit-up* 1 menit, *Pull-up*, dan *Shuttle run*.
  * Instruktur/Evaluator memasukkan data mentah (*raw value*, misal 35 kali push-up), sistem otomatis mengonversinya menjadi skor terstandarisasi (skala 0–100).

---

## 4. Alur Kerja End-to-End Sistem (Workflow)

```text
[1. SETUP CABANG & MASTER DATA]
Super Admin membuat Cabang -> Membuat User Admin Cabang -> Membuat Program & Subject.

[2. PENGELOMPOKAN AKADEMIK]
Super Admin/Admin Cabang membuat Batch (Periode Gelombang) 
  -> Membuat Ruang Kelas (Classroom) di cabang masing-masing
  -> Mendaftarkan Siswa ke Kelas (Classroom Enrollment).

[3. BELAJAR & MENGAJAR]
Admin membuat Jadwal Kelas (Class Schedule) 
  -> Instruktur mengajar & mencatat Presensi (Attendance)
  -> Siswa mengakses Materi Belajar (Learning Materials).

[4. PENYIAPAN BANK SOAL & UJIAN]
Instruktur/Admin membuat Soal (Questions) per Subject dengan aturan Standard/TKP
  -> Merangkai Template Ujian (Exam Template + Sections)
  -> Membuka Sesi Ujian (Exam Session) berbatas waktu dengan Token.

[5. PELAKSANAAN UJIAN CBT]
Siswa memasukkan Token -> Memulai Attempt (Timer Server Berjalan)
  -> Autosave Jawaban -> Submit Ujian -> Scoring Engine Otomatis Menghitung Nilai
  -> Siswa & Instruktur melihat Grafik Hasil & Peringkat Nasional/Cabang.
```
