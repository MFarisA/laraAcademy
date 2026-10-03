
<?php

use App\Enum\Academic\EnrollmentStatusEnum;
use App\Enum\Access\RoleRegistryEnum;
use App\Models\Academic\Batch;
use App\Models\Academic\Classroom;
use App\Models\Academic\Program;
use App\Models\Organization\Branch;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::findOrCreate(RoleRegistryEnum::SUPERADMIN->value);
    Role::findOrCreate(RoleRegistryEnum::STUDENT->value);
});

function createDummyClassroom(int $capacity = 2): Classroom
{
    $branch = Branch::create(['name' => 'Cabang Pusat', 'code' => 'PST-01']);
    $program = Program::create(['name' => 'CPNS 2026', 'code' => 'CPNS-26']);
    $batch = Batch::create([
        'program_id' => $program->id,
        'name' => 'Batch 1',
        'start_date' => now(),
        'end_date' => now()->addMonths(3),
    ]);

    return Classroom::create([
        'batch_id' => $batch->id,
        'branch_id' => $branch->id,
        'name' => 'Kelas Alpha',
        'capacity' => $capacity,
    ]);
}

test('super admin can enroll a student into classroom', function () {
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole(RoleRegistryEnum::SUPERADMIN->value);

    $student = User::factory()->create();
    $student->assignRole(RoleRegistryEnum::STUDENT->value);

    $classroom = createDummyClassroom(capacity: 10);

    $response = $this->actingAs($superAdmin)->post(route('classrooms.enrollments.store', $classroom), [
        'student_id' => $student->id,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('inertia.flash_data', [
        'toast' => ['type' => 'success', 'message' => 'Siswa berhasil didaftarkan ke kelas'],
    ]);

    $this->assertDatabaseHas('classroom_enrollments', [
        'classroom_id' => $classroom->id,
        'student_id' => $student->id,
        'status' => EnrollmentStatusEnum::ACTIVE->value,
    ]);
});

test('enrollment fails when classroom capacity is reached', function () {
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole(RoleRegistryEnum::SUPERADMIN->value);

    $classroom = createDummyClassroom(capacity: 1);

    $student1 = User::factory()->create();
    $student2 = User::factory()->create();

    // Daftarkan siswa 1 (kuota penuh: 1/1)
    $this->actingAs($superAdmin)->post(route('classrooms.enrollments.store', $classroom), [
        'student_id' => $student1->id,
    ]);

    // Daftarkan siswa 2 (seharusnya gagal dan melempar validation error)
    $response = $this->actingAs($superAdmin)->post(route('classrooms.enrollments.store', $classroom), [
        'student_id' => $student2->id,
    ]);

    $response->assertSessionHasErrors(['classroom_id']);

    $this->assertDatabaseMissing('classroom_enrollments', [
        'classroom_id' => $classroom->id,
        'student_id' => $student2->id,
    ]);
});

test('cannot enroll the same student twice into the same classroom', function () {
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole(RoleRegistryEnum::SUPERADMIN->value);

    $classroom = createDummyClassroom(capacity: 10);
    $student = User::factory()->create();

    // Daftarkan pertama kali
    $this->actingAs($superAdmin)->post(route('classrooms.enrollments.store', $classroom), [
        'student_id' => $student->id,
    ]);

    // Daftarkan kedua kali untuk siswa yang sama
    $response = $this->actingAs($superAdmin)->post(route('classrooms.enrollments.store', $classroom), [
        'student_id' => $student->id,
    ]);

    $response->assertSessionHasErrors(['student_id']);
});

test('super admin can update enrollment status', function () {
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole(RoleRegistryEnum::SUPERADMIN->value);

    $classroom = createDummyClassroom();
    $student = User::factory()->create();

    $classroom->students()->attach($student->id, [
        'status' => EnrollmentStatusEnum::ACTIVE->value,
        'enrolled_at' => now(),
    ]);

    $response = $this->actingAs($superAdmin)->patch(route('classrooms.enrollments.update', [$classroom, $student]), [
        'status' => EnrollmentStatusEnum::FINISHED->value,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('inertia.flash_data', [
        'toast' => ['type' => 'success', 'message' => 'Status Pendaftaran Siswa berhasil diperbarui.'],
    ]);

    $this->assertDatabaseHas('classroom_enrollments', [
        'classroom_id' => $classroom->id,
        'student_id' => $student->id,
        'status' => EnrollmentStatusEnum::FINISHED->value,
    ]);
});

test('destroying enrollment marks status as dropped instead of deleting row', function () {
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole(RoleRegistryEnum::SUPERADMIN->value);

    $classroom = createDummyClassroom();
    $student = User::factory()->create();

    $classroom->students()->attach($student->id, [
        'status' => EnrollmentStatusEnum::ACTIVE->value,
        'enrolled_at' => now(),
    ]);

    $response = $this->actingAs($superAdmin)->delete(route('classrooms.enrollments.destroy', [$classroom, $student]));

    $response->assertRedirect();
    $response->assertSessionHas('inertia.flash_data', [
        'toast' => ['type' => 'success', 'message' => 'Siswa telah dikeluarkan dari kelas'],
    ]);

    // Data tetap ada di database, tapi statusnya DROPPED
    $this->assertDatabaseHas('classroom_enrollments', [
        'classroom_id' => $classroom->id,
        'student_id' => $student->id,
        'status' => EnrollmentStatusEnum::DROPPED->value,
    ]);
});

test('non super admin is forbidden from managing enrollments', function () {
    $studentUser = User::factory()->create();
    $studentUser->assignRole(RoleRegistryEnum::STUDENT->value);

    $classroom = createDummyClassroom();
    $targetStudent = User::factory()->create();

    $response = $this->actingAs($studentUser)->post(route('classrooms.enrollments.store', $classroom), [
        'student_id' => $targetStudent->id,
    ]);

    $response->assertForbidden();
});
