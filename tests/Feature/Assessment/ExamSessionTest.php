<?php

use App\Enum\Access\RoleRegistryEnum;
use App\Enum\Assessment\ExamTemplateTypeEnum;
use App\Models\Academic\Program;
use App\Models\Assessment\Exam\ExamSession;
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
        'name' => 'Program Kedinasan',
        'code' => 'KEDINASAN-01',
    ]);

    $this->template = ExamTemplate::create([
        'program_id' => $this->program->id,
        'title' => 'Simulasi IPDN',
        'type' => ExamTemplateTypeEnum::KEDINASAN->value,
        'total_duration_minutes' => 100,
    ]);
});

test('super admin can view exam sessions list', function () {
    ExamSession::create([
        'exam_template_id' => $this->template->id,
        'title' => 'Sesi Gelombang 1',
        'token' => 'ABC123',
        'start_time' => now(),
        'end_time' => now()->addHours(2),
    ]);

    $this->withoutVite();

    $response = $this->actingAs($this->superAdmin)
        ->get(route('exam-sessions.index'));

    $response->assertOk();
});

test('super admin can create exam session and auto-generates token if omitted', function () {
    $payload = [
        'exam_template_id' => $this->template->id,
        'title' => 'Sesi Ujian Pagi',
        'start_time' => now()->toDateTimeString(),
        'end_time' => now()->addHours(3)->toDateTimeString(),
    ];

    $response = $this->actingAs($this->superAdmin)
        ->post(route('exam-sessions.store'), $payload);

    $response->assertSessionHas('inertia.flash_data', [
        'toast' => ['type' => 'success', 'message' => 'Sesi ujian berhasil dibuat.'],
    ]);

    $session = ExamSession::first();
    expect($session)->not->toBeNull();
    expect($session->title)->toBe('Sesi Ujian Pagi');
    expect($session->token)->toHaveLength(6);
});

test('super admin can regenerate session token', function () {
    $session = ExamSession::create([
        'exam_template_id' => $this->template->id,
        'title' => 'Sesi Siang',
        'token' => 'OLDTOK',
        'start_time' => now(),
        'end_time' => now()->addHours(2),
    ]);

    $response = $this->actingAs($this->superAdmin)
        ->post(route('exam-sessions.regenerate-token', $session));

    $response->assertSessionHas('inertia.flash_data', [
        'toast' => ['type' => 'success', 'message' => 'Token sesi berhasil diperbarui.'],
    ]);

    $session->refresh();
    expect($session->token)->not->toBe('OLDTOK');
    expect($session->token)->toHaveLength(6);
});

test('student cannot manage exam sessions', function () {
    $response = $this->actingAs($this->student)
        ->get(route('exam-sessions.index'));

    $response->assertForbidden();
});
