
<?php

use App\Enum\Access\RoleRegistryEnum;
use App\Enum\Learning\AttendanceStatusEnum;
use App\Enum\Learning\RoomTypeEnum;
use App\Models\Academic\Batch;
use App\Models\Academic\Classroom;
use App\Models\Academic\Program;
use App\Models\Academic\Subject;
use App\Models\Learning\Attendance;
use App\Models\Learning\ClassSchedule;
use App\Models\Organization\Branch;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::findOrCreate(RoleRegistryEnum::SUPERADMIN->value);
    Role::findOrCreate(RoleRegistryEnum::INSTRUCTOR->value);
    Role::findOrCreate(RoleRegistryEnum::STUDENT->value);
});

function createAttendanceTestEnvironment(): array
{
    $branch = Branch::create(['name' => 'Cabang Pusat', 'code' => 'PST-01']);
    $program = Program::create(['name' => 'Kedinasan 2026', 'code' => 'KDN-26']);
    $subject = Subject::create(['name' => 'Matematika Dasar', 'code' => 'MTK']);
    $batch = Batch::create([
        'program_id' => $program->id,
        'name' => 'Batch 1',
        'start_date' => now(),
        'end_date' => now()->addMonths(2),
    ]);
    $classroom = Classroom::create([
        'batch_id' => $batch->id,
        'branch_id' => $branch->id,
        'name' => 'Kelas Delta',
        'capacity' => 20,
    ]);

    $instructor = User::factory()->create();
    $instructor->assignRole(RoleRegistryEnum::INSTRUCTOR->value);

    $schedule = ClassSchedule::create([
        'classroom_id' => $classroom->id,
        'subject_id' => $subject->id,
        'instructor_id' => $instructor->id,
        'room_type' => RoomTypeEnum::OFFLINE->value,
        'scheduled_at' => now(),
        'duration_minutes' => 90,
    ]);

    $studentA = User::factory()->create();
    $studentA->assignRole(RoleRegistryEnum::STUDENT->value);
    $studentB = User::factory()->create();
    $studentB->assignRole(RoleRegistryEnum::STUDENT->value);

    // Daftarkan siswa ke kelas
    $classroom->students()->attach([
        $studentA->id => ['status' => 'active', 'enrolled_at' => now()],
        $studentB->id => ['status' => 'active', 'enrolled_at' => now()],
    ]);

    return compact('classroom', 'schedule', 'instructor', 'studentA', 'studentB');
}

test('super admin or instructor can record bulk attendance for a schedule', function () {
    $env = createAttendanceTestEnvironment();

    $superAdmin = User::factory()->create();
    $superAdmin->assignRole(RoleRegistryEnum::SUPERADMIN->value);

    $response = $this->actingAs($superAdmin)->post(route('schedules.attendances.record', $env['schedule']), [
        'attendances' => [
            ['student_id' => $env['studentA']->id, 'status' => AttendanceStatusEnum::PRESENT->value],
            ['student_id' => $env['studentB']->id, 'status' => AttendanceStatusEnum::SICK->value],
        ],
    ]);

    $response->assertRedirect(route('schedules.show', $env['schedule']));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('attendances', [
        'class_schedule_id' => $env['schedule']->id,
        'student_id' => $env['studentA']->id,
        'status' => AttendanceStatusEnum::PRESENT->value,
    ]);

    $this->assertDatabaseHas('attendances', [
        'class_schedule_id' => $env['schedule']->id,
        'student_id' => $env['studentB']->id,
        'status' => AttendanceStatusEnum::SICK->value,
    ]);

    // Pastikan verified_at terisi otomatis
    $attendance = Attendance::where('class_schedule_id', $env['schedule']->id)->first();
    expect($attendance->verified_at)->not->toBeNull();
});

test('recording attendance updates existing records without duplication', function () {
    $env = createAttendanceTestEnvironment();

    $superAdmin = User::factory()->create();
    $superAdmin->assignRole(RoleRegistryEnum::SUPERADMIN->value);

    // Input pertama: Student A dinyatakan Absent
    Attendance::create([
        'class_schedule_id' => $env['schedule']->id,
        'student_id' => $env['studentA']->id,
        'status' => AttendanceStatusEnum::ABSENT->value,
    ]);

    // Input ulang melalui endpoint: koreksi Student A menjadi Present
    $this->actingAs($superAdmin)->post(route('schedules.attendances.record', $env['schedule']), [
        'attendances' => [
            ['student_id' => $env['studentA']->id, 'status' => AttendanceStatusEnum::PRESENT->value],
        ],
    ]);

    // Pastikan status terupdate dan jumlah baris tetap 1
    $this->assertDatabaseHas('attendances', [
        'class_schedule_id' => $env['schedule']->id,
        'student_id' => $env['studentA']->id,
        'status' => AttendanceStatusEnum::PRESENT->value,
    ]);

    expect(Attendance::where('class_schedule_id', $env['schedule']->id)->count())->toBe(1);
});

test('can retrieve attendance summary for a classroom', function () {
    $this->withoutVite();
    $env = createAttendanceTestEnvironment();

    $superAdmin = User::factory()->create();
    $superAdmin->assignRole(RoleRegistryEnum::SUPERADMIN->value);

    // Simulasikan data kehadiran
    Attendance::create([
        'class_schedule_id' => $env['schedule']->id,
        'student_id' => $env['studentA']->id,
        'status' => AttendanceStatusEnum::PRESENT->value,
        'verified_at' => now(),
    ]);

    Attendance::create([
        'class_schedule_id' => $env['schedule']->id,
        'student_id' => $env['studentB']->id,
        'status' => AttendanceStatusEnum::ABSENT->value,
        'verified_at' => now(),
    ]);

    $response = $this->actingAs($superAdmin)->get(route('classrooms.attendance.summary', $env['classroom']));

    $response->assertOk();
    $response->assertInertia(
        fn ($page) => $page
            ->component('Learning/Attendance/Summary')
            ->has('classroom')
            ->has('summaries.data', 2)
    );
});
