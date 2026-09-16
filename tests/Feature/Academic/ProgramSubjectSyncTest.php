
<?php

use App\Enum\Access\RoleRegistryEnum;
use App\Models\Academic\Program;
use App\Models\Academic\Subject;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::findOrCreate(RoleRegistryEnum::SUPERADMIN->value);
    Role::findOrCreate(RoleRegistryEnum::STUDENT->value);
});

test('super admin can sync subjects to a program with min passing scores', function () {
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole(RoleRegistryEnum::SUPERADMIN->value);

    $program = Program::create(['name' => 'CPNS 2026', 'code' => 'CPNS-26']);
    $subjectA = Subject::create(['name' => 'Tes Wawasan Kebangsaan', 'code' => 'TWK']);
    $subjectB = Subject::create(['name' => 'Tes Intelegensia Umum', 'code' => 'TIU']);

    $response = $this->actingAs($superAdmin)->put(route('programs.subjects.update', $program), [
        'subjects' => [
            ['id' => $subjectA->id, 'min_passing_score' => 65],
            ['id' => $subjectB->id, 'min_passing_score' => 80],
        ],
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('program_subjects', [
        'program_id' => $program->id,
        'subject_id' => $subjectA->id,
        'min_passing_score' => 65,
    ]);

    $this->assertDatabaseHas('program_subjects', [
        'program_id' => $program->id,
        'subject_id' => $subjectB->id,
        'min_passing_score' => 80,
    ]);
});

test('syncing removes unlisted subjects from program', function () {
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole(RoleRegistryEnum::SUPERADMIN->value);

    $program = Program::create(['name' => 'Kedinasan', 'code' => 'KDN-26']);
    $subjectA = Subject::create(['name' => 'TWK', 'code' => 'TWK']);
    $subjectB = Subject::create(['name' => 'TIU', 'code' => 'TIU']);

    // Awalnya punya 2 subject
    $program->subjects()->attach([
        $subjectA->id => ['min_passing_score' => 65],
        $subjectB->id => ['min_passing_score' => 80],
    ]);

    // Lakukan sync hanya dengan subject B
    $this->actingAs($superAdmin)->put(route('programs.subjects.update', $program), [
        'subjects' => [
            ['id' => $subjectB->id, 'min_passing_score' => 85],
        ],
    ]);

    $this->assertDatabaseMissing('program_subjects', [
        'program_id' => $program->id,
        'subject_id' => $subjectA->id,
    ]);

    $this->assertDatabaseHas('program_subjects', [
        'program_id' => $program->id,
        'subject_id' => $subjectB->id,
        'min_passing_score' => 85,
    ]);
});

test('non super admin is forbidden from syncing program subjects', function () {
    $student = User::factory()->create();
    $student->assignRole(RoleRegistryEnum::STUDENT->value);

    $program = Program::create(['name' => 'CPNS 2026', 'code' => 'CPNS-26']);
    $subject = Subject::create(['name' => 'TWK', 'code' => 'TWK']);

    $response = $this->actingAs($student)->put(route('programs.subjects.update', $program), [
        'subjects' => [
            ['id' => $subject->id, 'min_passing_score' => 65],
        ],
    ]);

    $response->assertForbidden();
});
