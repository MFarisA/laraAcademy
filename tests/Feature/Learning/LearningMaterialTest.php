<?php

use App\Enum\Access\RoleRegistryEnum;
use App\Models\Academic\Batch;
use App\Models\Academic\Classroom;
use App\Models\Academic\Program;
use App\Models\Academic\Subject;
use App\Models\Learning\LearningMaterial;
use App\Models\Organization\Branch;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::findOrCreate(RoleRegistryEnum::SUPERADMIN->value);
    Role::findOrCreate(RoleRegistryEnum::INSTRUCTOR->value);
    Role::findOrCreate(RoleRegistryEnum::STUDENT->value);
    Role::findOrCreate(RoleRegistryEnum::ADMINBRANCH->value);

    Storage::fake('local');
});

function createMaterialTestSetup(): array
{
    $branch = Branch::create(['name' => 'Cabang Utama', 'code' => 'CBG-01']);
    $program = Program::create(['name' => 'Program Kedinasan', 'code' => 'KDN-2026']);
    $subject = Subject::create(['name' => 'Tes Wawasan Kebangsaan', 'code' => 'TWK']);

    // Hubungkan Subject ke Program via pivot
    $program->subjects()->attach($subject->id, ['min_passing_score' => 65]);

    $batch = Batch::create([
        'program_id' => $program->id,
        'name' => 'Batch 2026',
        'start_date' => now(),
        'end_date' => now()->addMonths(3),
    ]);

    $classroom = Classroom::create([
        'batch_id' => $batch->id,
        'branch_id' => $branch->id,
        'name' => 'Kelas TWK Alpha',
        'capacity' => 30,
    ]);

    $instructor = User::factory()->create();
    $instructor->assignRole(RoleRegistryEnum::INSTRUCTOR->value);

    $studentEnrolled = User::factory()->create();
    $studentEnrolled->assignRole(RoleRegistryEnum::STUDENT->value);

    $studentNotEnrolled = User::factory()->create();
    $studentNotEnrolled->assignRole(RoleRegistryEnum::STUDENT->value);

    // Daftarkan siswa ke kelas
    $classroom->students()->attach($studentEnrolled->id, [
        'status' => 'active',
        'enrolled_at' => now(),
    ]);

    return compact(
        'branch',
        'program',
        'subject',
        'batch',
        'classroom',
        'instructor',
        'studentEnrolled',
        'studentNotEnrolled',
    );
}

test('instructor can upload learning material and file is stored in local storage', function () {
    $setup = createMaterialTestSetup();

    $file = UploadedFile::fake()->create('modul-pancasila.pdf', 500, 'application/pdf');

    $response = $this->actingAs($setup['instructor'])->post(route('materials.store'), [
        'subject_id' => $setup['subject']->id,
        'program_id' => $setup['program']->id,
        'title' => 'Modul 01: Pancasila dan UUD 1945',
        'type' => 'pdf',
        'is_downloadable' => true,
        'file' => $file,
    ]);

    $response->assertSessionHas('success');

    $this->assertDatabaseHas('learning_materials', [
        'title' => 'Modul 01: Pancasila dan UUD 1945',
        'subject_id' => $setup['subject']->id,
        'program_id' => $setup['program']->id,
        'type' => 'pdf',
        'is_downloadable' => true,
    ]);

    $material = LearningMaterial::first();
    expect($material)->not->toBeNull();
    Storage::disk('local')->assertExists($material->file_url);
});

test('enrolled student can stream learning material using a valid signed URL', function () {
    $setup = createMaterialTestSetup();

    // Buat file materi di storage
    $filePath = 'materials/'.$setup['subject']->id.'/materi.pdf';
    Storage::disk('local')->put($filePath, 'PDF dummy content');

    $material = LearningMaterial::create([
        'subject_id' => $setup['subject']->id,
        'program_id' => $setup['program']->id,
        'title' => 'Materi Tes Wawasan Kebangsaan',
        'type' => 'pdf',
        'file_url' => $filePath,
        'is_downloadable' => false,
    ]);

    $signedUrl = URL::temporarySignedRoute(
        'materials.stream',
        now()->addMinutes(30),
        ['material' => $material->id]
    );

    $response = $this->actingAs($setup['studentEnrolled'])->get($signedUrl);

    $response->assertOk();
    $response->assertHeader('content-disposition', 'inline; filename="Materi Tes Wawasan Kebangsaan.pdf"');
});

test('student not enrolled in the subject program cannot stream the material', function () {
    $setup = createMaterialTestSetup();

    $filePath = 'materials/'.$setup['subject']->id.'/materi.pdf';
    Storage::disk('local')->put($filePath, 'PDF dummy content');

    $material = LearningMaterial::create([
        'subject_id' => $setup['subject']->id,
        'program_id' => $setup['program']->id,
        'title' => 'Materi Rahasia',
        'type' => 'pdf',
        'file_url' => $filePath,
        'is_downloadable' => true,
    ]);

    $signedUrl = URL::temporarySignedRoute(
        'materials.stream',
        now()->addMinutes(30),
        ['material' => $material->id]
    );

    $response = $this->actingAs($setup['studentNotEnrolled'])->get($signedUrl);

    $response->assertForbidden();
});

test('tampered or unsigned URL is rejected with 403', function () {
    $setup = createMaterialTestSetup();

    $filePath = 'materials/'.$setup['subject']->id.'/materi.pdf';
    Storage::disk('local')->put($filePath, 'PDF dummy content');

    $material = LearningMaterial::create([
        'subject_id' => $setup['subject']->id,
        'program_id' => $setup['program']->id,
        'title' => 'Materi Bebas',
        'type' => 'pdf',
        'file_url' => $filePath,
        'is_downloadable' => true,
    ]);

    // Akses tanpa signed query
    $unsignedUrl = route('materials.stream', ['material' => $material->id]);

    $response = $this->actingAs($setup['studentEnrolled'])->get($unsignedUrl);

    $response->assertForbidden();
});

test('enrolled student cannot download material when is_downloadable is false', function () {
    $setup = createMaterialTestSetup();

    $filePath = 'materials/'.$setup['subject']->id.'/materi.pdf';
    Storage::disk('local')->put($filePath, 'PDF dummy content');

    $material = LearningMaterial::create([
        'subject_id' => $setup['subject']->id,
        'program_id' => $setup['program']->id,
        'title' => 'Materi Non Downloadable',
        'type' => 'pdf',
        'file_url' => $filePath,
        'is_downloadable' => false,
    ]);

    $signedUrl = URL::temporarySignedRoute(
        'materials.stream',
        now()->addMinutes(30),
        ['material' => $material->id, 'download' => 1]
    );

    $response = $this->actingAs($setup['studentEnrolled'])->get($signedUrl);

    $response->assertForbidden();
});

test('enrolled student can download material when is_downloadable is true', function () {
    $setup = createMaterialTestSetup();

    $filePath = 'materials/'.$setup['subject']->id.'/materi.pdf';
    Storage::disk('local')->put($filePath, 'PDF dummy content');

    $material = LearningMaterial::create([
        'subject_id' => $setup['subject']->id,
        'program_id' => $setup['program']->id,
        'title' => 'Materi Bisa Diunduh',
        'type' => 'pdf',
        'file_url' => $filePath,
        'is_downloadable' => true,
    ]);

    $signedUrl = URL::temporarySignedRoute(
        'materials.stream',
        now()->addMinutes(30),
        ['material' => $material->id, 'download' => 1]
    );

    $response = $this->actingAs($setup['studentEnrolled'])->get($signedUrl);

    $response->assertOk();
    expect($response->headers->get('content-disposition'))->toContain('attachment;');
});

test('instructor can download material even when is_downloadable is false', function () {
    $setup = createMaterialTestSetup();

    $filePath = 'materials/'.$setup['subject']->id.'/materi.pdf';
    Storage::disk('local')->put($filePath, 'PDF dummy content');

    $material = LearningMaterial::create([
        'subject_id' => $setup['subject']->id,
        'program_id' => $setup['program']->id,
        'title' => 'Materi Guru',
        'type' => 'pdf',
        'file_url' => $filePath,
        'is_downloadable' => false,
    ]);

    $signedUrl = URL::temporarySignedRoute(
        'materials.stream',
        now()->addMinutes(30),
        ['material' => $material->id, 'download' => 1]
    );

    $response = $this->actingAs($setup['instructor'])->get($signedUrl);

    $response->assertOk();
    expect($response->headers->get('content-disposition'))->toContain('attachment;');
});
