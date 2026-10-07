<?php

use App\Enum\Access\RoleRegistryEnum;
use App\Enum\Assessment\ExamTemplateTypeEnum;
use App\Models\Academic\Program;
use App\Models\Academic\Subject;
use App\Models\Assessment\Exam\ExamTemplate;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::findOrCreate(RoleRegistryEnum::SUPERADMIN->value);
    Role::findOrCreate(RoleRegistryEnum::STUDENT->value);

    $this->superAdmin = User::factory()->create([
        'email_verified_at' => now(),
    ]);
    $this->superAdmin->assignRole(RoleRegistryEnum::SUPERADMIN->value);

    $this->student = User::factory()->create([
        'email_verified_at' => now(),
    ]);
    $this->student->assignRole(RoleRegistryEnum::STUDENT->value);

    $this->program = Program::create([
        'name' => 'Program Intensif CPNS 2026',
        'code' => 'CPNS-2026',
    ]);

    $this->subject = Subject::create([
        'name' => 'Tes Wawasan Kebangsaan',
        'code' => 'TWK',
    ]);
});

test('super admin can view exam templates list', function () {
    ExamTemplate::create([
        'program_id' => $this->program->id,
        'title' => 'Simulasi SKD 1',
        'type' => ExamTemplateTypeEnum::SKD->value,
        'total_duration_minutes' => 100,
    ]);

    $this->withoutVite();

    $response = $this->actingAs($this->superAdmin)
        ->get(route('exam-templates.index'));

    $response->assertOk();
});

test('super admin can create exam template with nested sections', function () {
    $payload = [
        'program_id' => $this->program->id,
        'title' => 'Tryout Akbar SKD',
        'type' => ExamTemplateTypeEnum::SKD->value,
        'total_duration_minutes' => 100,
        'sections' => [
            [
                'subject_id' => $this->subject->id,
                'title' => 'Seksi TWK',
                'passing_grade' => 65,
                'duration_minutes' => 30,
                'order_index' => 1,
            ],
        ],
    ];

    $response = $this->actingAs($this->superAdmin)
        ->post(route('exam-templates.store'), $payload);

    $response->assertSessionHas('inertia.flash_data', [
        'toast' => ['type' => 'success', 'message' => 'Template ujian berhasil dibuat.'],
    ]);

    $this->assertDatabaseHas('exam_templates', [
        'program_id' => $this->program->id,
        'title' => 'Tryout Akbar SKD',
        'type' => ExamTemplateTypeEnum::SKD->value,
    ]);

    $this->assertDatabaseHas('exam_sections', [
        'subject_id' => $this->subject->id,
        'title' => 'Seksi TWK',
        'passing_grade' => 65,
    ]);
});

test('super admin can update an exam template', function () {
    $template = ExamTemplate::create([
        'program_id' => $this->program->id,
        'title' => 'Judul Lama',
        'type' => ExamTemplateTypeEnum::SKD->value,
        'total_duration_minutes' => 90,
    ]);

    $payload = [
        'program_id' => $this->program->id,
        'title' => 'Judul Baru Diperbarui',
        'type' => ExamTemplateTypeEnum::SKD->value,
        'total_duration_minutes' => 100,
    ];

    $response = $this->actingAs($this->superAdmin)
        ->put(route('exam-templates.update', $template), $payload);

    $response->assertSessionHas('inertia.flash_data', [
        'toast' => ['type' => 'success', 'message' => 'Template ujian berhasil dibuat.'],
    ]);

    $this->assertDatabaseHas('exam_templates', [
        'id' => $template->id,
        'title' => 'Judul Baru Diperbarui',
        'total_duration_minutes' => 100,
    ]);
});

test('super admin can delete an exam template', function () {
    $template = ExamTemplate::create([
        'program_id' => $this->program->id,
        'title' => 'Template Dihapus',
        'type' => ExamTemplateTypeEnum::SKD->value,
        'total_duration_minutes' => 60,
    ]);

    $response = $this->actingAs($this->superAdmin)
        ->delete(route('exam-templates.destroy', $template));

    $response->assertSessionHas('inertia.flash_data', [
        'toast' => ['type' => 'success', 'message' => 'Template ujian berhasil dibuat.'],
    ]);

    $this->assertDatabaseMissing('exam_templates', [
        'id' => $template->id,
    ]);
});

test('student cannot manage exam templates', function () {
    $response = $this->actingAs($this->student)
        ->get(route('exam-templates.index'));

    $response->assertForbidden();
});
