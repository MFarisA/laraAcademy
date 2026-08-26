# laraAcademy — Flow Eksekusi Backend

Panduan langkah demi langkah untuk mengeksekusi `features.md`.
Setiap langkah punya: **aksi konkret → file yang dibuat/diubah → gerbang verifikasi**.

> Aturan main: satu fase selesai = test hijau + pint clean, baru lanjut fase berikutnya.
> Jalankan test via Sail: `./vendor/bin/sail artisan test --compact`
> Format kode: `vendor/bin/pint --dirty --format agent`

## Status Awal (sudah selesai)

- [x] Fortify auth lengkap (login/register/reset/2FA/passkeys)
- [x] 25 migration + 21 model + factory + seeder skeleton
- [x] Audit & perbaikan relasi model (lihat `models.md`)
- [x] Spatie Permission v8.3 ter-install, migrasi `create_permission_tables` ada
- [x] Trait `HasRoles` aktif di model User

---

## FASE 1 — RBAC & Fondasi Organisasi

### Langkah 1.1 — Registrasi middleware Spatie
**File**: `bootstrap/app.php`

Tambahkan alias di dalam `->withMiddleware()`:
- `'role' => RoleMiddleware::class`
- `'permission' => PermissionMiddleware::class`
- `'role_or_permission' => RoleOrPermissionMiddleware::class`

Verifikasi: `php artisan route:list` tetap jalan tanpa error.

### Langkah 1.2 — Enum Role (opsional tapi disarankan)
**File baru**: `app/Enums/Role.php` (`SuperAdmin`, `AdminCabang`, `Instruktur`, `Siswa`) — TitleCase key sesuai konvensi.
Dipakai seeder & policy agar tidak ada string magic tersebar.

### Langkah 1.3 — Seeder Roles & Permissions
**File baru**: `database/seeders/RolesAndPermissionsSeeder.php`

- Buat 4 roles di atas
- Permission per modul, pola `resource.action`: `branches.manage`, `users.manage`, `programs.manage`, `subjects.manage`, `batches.manage`, `classrooms.manage`, `enrollments.manage`, `schedules.manage`, `attendances.manage`, `materials.manage`, `questions.manage`, `exams.manage`, `exams.take`, `physical.manage`
- Mapping:
  - `super-admin`: semua permission (atau pakai Gate::before bypass — lihat Langkah 1.5)
  - `admin-cabang`: semua `.manage` kecuali `branches.manage`
  - `instruktur`: `schedules.manage`, `attendances.manage`, `materials.manage`, `physical.manage`
  - `siswa`: `exams.take`
- Panggil dari `DatabaseSeeder` **sebelum** seeding data lain
- Reset cache permission setelah seed: `app(PermissionRegistrar::class)->forgetCachedPermissions()`

Verifikasi: `php artisan db:seed` sukses; tinker cek `Role::count() == 4`.

### Langkah 1.4 — Assign role otomatis saat registrasi
**File**: `app/Actions/Fortify/CreateNewUser.php`

Setelah `User::create(...)`: `$user->assignRole(Role::Siswa)`.
Ini SATU-satunya perubahan yang dibutuhkan di alur Fortify.

Verifikasi: test Pest registrasi → assert `$user->hasRole('siswa')`.

### Langkah 1.5 — Super-admin bypass
**File**: `app/Providers/AppServiceProvider.php`

Di `boot()`: `Gate::before(fn ($user) => $user->hasRole('super-admin') ? true : null);`
Dengan ini super-admin tidak perlu daftar permission manual.

### Langkah 1.6 — Seeder akun awal
**File**: `database/seeders/DatabaseSeeder.php`

Ganti "Test User" dengan akun nyata: 1 super-admin (kredensial via env/variabel seeder), lalu panggil seeder domain yang sudah ada (Branch, Program, dst.).

### Langkah 1.7 — Redirect pasca-login per role
**File**: buat `app/Http/Responses/LoginResponse.php` (implement `Laravel\Fortify\Contracts\LoginResponse`), bind di `AppServiceProvider`.
Logika: super-admin/admin-cabang → dashboard admin; instruktur → jadwal; siswa → dashboard siswa.
(Frontend route-nya menyusul; backend cukup return redirect target yang benar.)

### Langkah 1.8 — Branch scoping untuk admin-cabang
**File baru**: `app/Http/Middleware/EnsureBranchContext.php` (set context cabang user ke `Context`/session) + global scope atau trait `BelongsToBranch` pada model ber-cabang.
Bisa ditunda sampai CRUD organisasi (Langkah 2.x) ada — jangan bikin scope tanpa konsumen.

### Gerbang Fase 1
- [ ] Test: registrasi → role siswa
- [ ] Test: user tanpa role ditolak akses route terproteksi (403)
- [ ] Test: super-admin lolos semua gate
- [ ] pint clean, semua test lama tetap hijau

---

## FASE 2 — CRUD Organisasi (features.md §2)

Urutan per resource (ulangi pola yang sama):

### Langkah 2.1 — Cabang
1. `php artisan make:class App/Actions/Branches/CreateBranchAction` (atau controller standar) — ikuti pola lorisleiva/actions
2. FormRequest: `StoreBranchRequest`, `UpdateBranchRequest` (validasi `code` unik)
3. Route group `->middleware('role:super-admin')` + policy `BranchPolicy`
4. Test Pest: create/read/update/delete + validasi duplikat code + 403 untuk non-admin

### Langkah 2.2 — User Management
1. Migration: kolom `is_active boolean default true` di users (+ cast)
2. Controller + FormRequests (store/update/assign-role/toggle-active)
3. Policy: admin-cabang hanya meliputi users satu cabangnya
4. Filter+paginasi: query param `role`, `branch_id`, `search`, `active`
5. Test: assign role, toggle active, scoping antar-cabang

---

## FASE 3 — Akademik (features.md §3)

Kerjakan berurutan mengikuti dependensi FK:

| Urut | Resource | Poin kritis |
|---|---|---|
| 3.1 | Program CRUD | — |
| 3.2 | Subject CRUD | — |
| 3.3 | Sync program↔subject | endpoint sync dengan payload `{subject_id: min_passing_score}`; transaksi DB |
| 3.4 | Batch CRUD | wajib `program_id` valid |
| 3.5 | Classroom CRUD | validasi kapasitas > 0 |
| 3.6 | Enrollment | **atomic lock kuota** (Cache::lock / lockForUpdate), status enum, enrolled_at otomatis; tolak jika penuh |
| 3.7 | Class Schedule CRUD | instructor harus punya role instruktur |
| 3.8 | Attendance | record massal per jadwal; verifikasi instruktur set `verified_at`; enum status |
| 3.9 | Rekap kehadiran | query agregat persentase per siswa/kelas |
| 3.10 | Learning Materials | upload storage + signed URL; enforce `is_downloadable`; gate akses via enrollment |

Gerbang: test tiap nomor; khusus 3.6 test race condition (dua enrollment bersamaan pada 1 kursi tersisa).

---

## FASE 4 — Bank Soal (features.md §4)

| Urut | Langkah | Detail |
|---|---|---|
| 4.1 | Migration tambahan | soft deletes `questions` (`deleted_at`); enum PHP `GradingRule {Standard, Tkp}` + cast di model |
| 4.2 | CRUD Question | nested create options (payload berisi array opsi); transaksi |
| 4.3 | Validasi opsi per rule | `Standard`: tepat ≥1 opsi `is_correct`; `Tkp`: semua weight 1–5, abaikan is_correct |
| 4.4 | Filter & search | by subject, difficulty, grading_rule; full-text PostgreSQL |
| 4.5 | (Opsional) Import Excel | package laravel-excel — tunda ke fase polish |

---

## FASE 5 — Ujian CBT (features.md §5) — INTI PRODUK

Kerjakan bertahap, JANGAN sekali jalan:

### 5A. Authoring (template & section)
1. CRUD exam_templates (+ type enum)
2. CRUD exam_sections (passing_grade, duration, order_index)
3. Attach questions ↔ sections (pivot + order_index)

### 5B. Sesi
4. CRUD exam_sessions + generate token unik (`Str::random`, unique index)
5. Endpoint "masuk sesi": validasi token + window waktu + keanggotaan classroom/publik

### 5C. Attempt
6. Migration `exam_section_results` + index `classroom_enrollments(student_id)` + enum `ExamAttemptStatus`
7. Start attempt: transaksi + lockForUpdate anti double-attempt; set started_at
8. Autosave jawaban: upsert exam_answers (rate limited)
9. Resume attempt in_progress; deadline = server-side (started_at + durasi)
10. Submit: kunci attempt, trigger scoring
11. Auto-submit expired: scheduled command scan attempt melewati deadline

### 5D. Scoring Engine
12. Action `ScoreAttemptAction`: hitung per section (rule Standard vs Tkp), tulis exam_section_results, agregasi total_score + is_passed ke attempts
13. Semua dalam DB transaction; idempotent (submit ulang tidak dobel)

Gerbang: unit test scoring untuk kedua rule + feature test alur start→autosave→submit→hasil.

---

## FASE 6 — Ranking & Leaderboard (features.md §5.5–5.6)
1. Job `CalculateRanksJob` (queued): dipanggil setelah submit; ranking per session, tie-break total_score DESC → durasi ASC → id ASC; hanya completed
2. Leaderboard endpoint (paginate, scope national/branch)
3. Endpoint hasil detail siswa (skor per subtes, riwayat attempt)
4. Test: konsistensi rank saat skor seri, job queued assertion

---

## FASE 7 — Penilaian Fisik (features.md §6)
1. (Opsional) tabel referensi `physical_metrics` + FK
2. CRUD assessment + input skor metrik (transaksi)
3. Formula konversi config-driven → calculated_score; total otomatis
4. Riwayat per siswa; test

---

## FASE 8 — Polish & Non-Fungsional (features.md §7 sisa)
- Soft deletes: exam_attempts, physical_assessments (questions sudah di Fase 4)
- Rate limiting finalisasi endpoint ujian
- Import/export Excel, analisis butir soal, recurring schedule, randomisasi soal per attempt
- (Opsional) spatie/laravel-activitylog
- Review N+1 (pastikan eager loading di endpoint list), index audit ulang

---

## Checklist Harian Saat Coding
1. Tulis/baca fitur di features.md → temukan langkahnya di flow.md ini
2. `php artisan make:...` dulu (migration/model/test), isi kode
3. `./vendor/bin/sail artisan test --compact --filter=NamaFitur`
4. `vendor/bin/pint --dirty --format agent`
5. Centang item di features.md bila selesai
