# laraAcademy — Backend Feature Specification

Aplikasi LMS & CBT Ujian: gabungan Ganesha Operation (bimbel multi-cabang) + Ruangguru (bank soal, materi, simulasi tryout), khusus persiapan **Ujian Kedinasan (CAT BKN), CPNS, dan SNBT**.

Stack: Laravel 12 · PostgreSQL · Fortify (auth) · Spatie Permission (RBAC) · Inertia React (frontend menyusul).

> Dokumen ini hanya mencakup **backend**. Setiap item harus punya test Pest dan FormRequest validation.

---

## 1. Autentikasi & RBAC

### 1.1 Auth (fondasi Fortify sudah ada)

- [x] Login / logout / registrasi siswa / reset password / verifikasi email
- [x] 2FA (TOTP + recovery codes)
- [x] Passkeys
- [ ] Redirect pasca-login berdasarkan role

### 1.2 RBAC dengan Spatie Permission

- [ ] Install `spatie/laravel-permission`, jalankan migrasi bawaan
- [ ] Roles:
    - `super-admin` — akses penuh lintas cabang
    - `admin-cabang` — kelola data terbatas pada cabangnya
    - `instruktur` — jadwal, absensi, materi, input nilai fisik
    - `siswa` — ikut kelas, ujian, lihat hasil & ranking
- [ ] Seeder roles + permissions per modul
- [ ] Assign role default `siswa` saat registrasi
- [ ] **Branch scoping**: admin-cabang hanya melihat data cabangnya (global scope atau context middleware)
- [ ] Policies untuk semua resource

---

## 2. Manajemen Organisasi

### 2.1 Cabang (`branches`)

- [ ] CRUD cabang (super-admin saja), validasi `code` unik

### 2.2 User Management

- [ ] CRUD user oleh admin (admin-cabang terbatas cabangnya)
- [ ] Assign role & branch ke user
- [ ] Filter + paginasi daftar user
- [ ] Toggle status aktif/nonaktif akun (perlu kolom `is_active`)

---

## 3. Manajemen Akademik

### 3.1 Program & Mata Pelajaran

- [ ] CRUD `programs` (mis. "Persiapan CPNS Kedinasan", "SNBT 2027")
- [ ] CRUD `subjects` (TWK, TIU, TKP, Matematika SMA, dst.)
- [ ] Mapping Program ↔ Subject via pivot `program_subjects` dengan `min_passing_score`
- [ ] Endpoint attach/detach/sync subjects ke program beserta skor minimumnya

### 3.2 Batch & Kelas

- [ ] CRUD `batches` per program (angkatan, tanggal mulai/selesai)
- [ ] CRUD `classrooms` per batch + branch, validasi kapasitas
- [ ] Enrollment siswa ke kelas: `classroom_enrollments` (status: active/finished/dropped, `enrolled_at` otomatis)
- [ ] Validasi kuota: tolak enrollment jika kelas penuh (`capacity`) — pakai atomic lock
- [ ] Pindah kelas / dropout siswa (ubah status, bukan hapus)

### 3.3 Jadwal & Absensi

- [ ] CRUD `class_schedules` (offline/online, meeting link, durasi)
- [ ] Assign instruktur ke jadwal (`instructor_id`)
- [ ] Recurring schedule generator (opsional fase 2): generate jadwal mingguan dari template
- [ ] Record absensi per siswa per jadwal (`attendances`: present/absent/excused/sick/late)
- [ ] Verifikasi absensi oleh instruktur (`verified_at`)
- [ ] Rekap kehadiran per siswa & per kelas (persentase kehadiran)
- [ ] Notifikasi (queued) pengingat jadwal ke siswa — opsional fase 2

### 3.4 Materi Belajar

- [ ] CRUD `learning_materials` (upload file → storage, tipe: pdf/video/link)
- [ ] Flag `is_downloadable` di-enforce saat serve file (signed URL)
- [ ] Akses materi hanya untuk siswa terdaftar di subject/program terkait

---

## 4. Bank Soal

- [ ] CRUD `questions` per subject (text, image, tingkat kesulitan)
- [ ] CRUD `question_options` per question (label, text, `is_correct`, `weight_score`)
- [ ] **Enum `grading_rule`**: `Standard` (skor hanya opsi benar) vs `Tkp` (semua opsi bernilai 1–5, tidak ada jawaban salah)
- [ ] Import soal massal via Excel/CSV (paket: laravel-excel) — opsional fase 2
- [ ] Filter bank soal: per subject, difficulty, grading rule; search full-text
- [ ] Soft delete questions (jaga integritas riwayat jawaban)

## 5. Modul Ujian CBT (inti produk)

### 5.1 Template Ujian

- [ ] CRUD `exam_templates` per program (type: simulasi-skd, tryout-snbt, dst., total durasi)
- [ ] CRUD `exam_sections` per template: subject, `passing_grade`, `duration_minutes`, `order_index`
- [ ] Attach soal ke section via `exam_section_questions` (+ urutan)

### 5.2 Sesi Ujian

- [ ] CRUD `exam_sessions`: pilih template, target classroom (nullable = nasional/publik), token akses, window waktu (`start_time`–`end_time`)
- [ ] Generate & regenerasi token unik per sesi
- [ ] Autorisasi masuk sesi: siswa terdaftar di classroom ATAU sesi publik + token cocok + dalam window waktu

### 5.3 Attempt & Pelaksanaan Ujian

- [ ] Start attempt: cek tidak ada attempt `in_progress` aktif lain di sesi yang sama, buat attempt + `started_at`
- [ ] **Server-side deadline**: batas submit = `started_at + durasi section/template`; client time tidak dipercaya
- [ ] Simpan jawaban autosave: upsert `exam_answers` (selected_option_id, time_spent_seconds)
- [ ] Randomisasi urutan soal & opsi per attempt (simpan seed/order di attempt atau jawaban) — fase 2
- [ ] Resume: attempt `in_progress` bisa dilanjutkan selama belum lewat deadline server
- [ ] Auto-submit: job/command menandai attempt expired ketika deadline lewat
- [ ] Submit attempt: validasi kelengkapan, hitung nilai, kunci jawaban (tidak bisa diedit lagi)

### 5.4 Mesin Penilaian (scoring engine)

- [ ] Service/Action `ScoreAttemptAction`:
    - Rule `Standard`: skor = bobot opsi benar yang dipilih; benar sesuai weight, salah 0
    - Rule `Tkp`: skor = `weight_score` opsi terpilih per soal (rentang 1–5); total maksimum = 5 × jumlah soal TKP
    - Normalisasi skor per section sesuai kebutuhan (mis. skala 100)
- [ ] Hitung skor **per section** dan simpan ke tabel baru `exam_section_results`:
    - `exam_attempt_id`, `exam_section_id`, `score`, `is_passed` (vs `passing_grade` section), unique(attempt, section)
- [ ] Agregasi ke `exam_attempts`: `total_score`, `is_passed` (lulus SEMUA section wajib), status `completed`

### 5.5 Ranking Nasional & Cabang

- [ ] Job queued `CalculateRanksJob` setelah submit (atau scheduled batch):
    - `national_rank`: ranking siswa antar semua cabang untuk sesi yang sama
    - `branch_rank`: ranking dibatasi users satu cabang
- [ ] Tie-breaking yang deterministik (mis. total_score DESC, waktu pengerjaan ASC, id ASC)
- [ ] Hanya attempt `completed` yang masuk peringkat
- [ ] Leaderboard endpoint: per sesi, per cabang, dengan paginasi

### 5.6 Hasil & Rekap

- [ ] Detail hasil attempt milik siswa: skor per subtes, lulus/tidak per subtes, rank nasional & cabang, grafik historik (riwayat attempt)
- [ ] Rekap untuk admin/instruktur: rata-rata per section, distribusi jawaban per soal (analisis butir soal) — fase 2
- [ ] Export hasil ke Excel/PDF — fase 2

---

## 6. Penilaian Fisik / Kesamaptaan

- [ ] CRUD `physical_assessments` (evaluator = instruktur; student; tanggal; notes)
- [ ] Input skor per metrik ke `physical_test_scores` (metric_name, raw_value, calculated_score)
- [ ] Tabel referensi metrik standar (push-up, sit-up, bleep test, dll.) agar `metric_name` konsisten — pertimbangkan tabel `physical_metrics` + FK
- [ ] Formula konversi raw_value → calculated_score per metrik (config-driven)
- [ ] Total otomatis `total_physical_score`
- [ ] Riwayat penilaian fisik per siswa

---

## 7. Infrastruktur & Non-Fungsional

- [ ] Migrasi tambahan:
    - `exam_section_results`
    - index `classroom_enrollments(student_id)`
    - kolom `is_active` di users (bila dipakai)
    - enum/check constraint untuk kolom status (atau minimal enum PHP + validasi)
- [ ] Enums PHP: `GradingRule`, `ExamAttemptStatus`, `EnrollmentStatus`, `AttendanceStatus`, `RoomType`
- [ ] Soft deletes di tabel bernilai riwayat (questions, attempts, assessments)
- [ ] Rate limiting endpoint ujian (start/autosave/submit)
- [ ] DB transactions di scoring & enrollment (atomic lock untuk kuota)
- [ ] Queued jobs: ranking, auto-submit, notifikasi
- [ ] Audit log aktivitas admin (spatie/laravel-activitylog) — opsional
- [ ] Feature tests Pest untuk setiap fitur di atas; factories lengkap dengan states

---

## Urutan Eksekusi yang Disarankan

| Fase | Scope                                                                      |
| ---- | -------------------------------------------------------------------------- |
| 1    | RBAC + policies + seeder, user management, CRUD organisasi                 |
| 2    | Akademik: program/subject/batch/classroom/enrollment/jadwal/absensi/materi |
| 3    | Bank soal + enums (`grading_rule`)                                         |
| 4    | Ujian: template → sesi → attempt → scoring engine → `exam_section_results` |
| 5    | Ranking jobs + leaderboard                                                 |
| 6    | Penilaian fisik                                                            |
| 7    | Polish: import/export, analisis butir, recurring schedule, randomisasi     |

Setiap fase selesai = test hijau + pint clean sebelum lanjut.
