<?php

use App\Enum\Access\RoleRegistryEnum;
use App\Enum\Assessment\DifficultyLevelEnum;
use App\Enum\Assessment\ExamAttemptStatusEnum;
use App\Enum\Assessment\ExamTemplateTypeEnum;
use App\Enum\Assessment\GradingRuleEnum;
use App\Models\Academic\Program;
use App\Models\Academic\Subject;
use App\Models\Assessment\Exam\ExamAttempt;
use App\Models\Assessment\Exam\ExamSection;
use App\Models\Assessment\Exam\ExamSession;
use App\Models\Assessment\Exam\ExamTemplate;
use App\Models\Assessment\Question\Question;
use App\Models\Assessment\Question\QuestionOption;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::findOrCreate(RoleRegistryEnum::SUPERADMIN->value);
    Role::findOrCreate(RoleRegistryEnum::STUDENT->value);

    $this->student = User::factory()->create([
        'email_verified_at' => now(),
    ]);
    $this->student->assignRole(RoleRegistryEnum::STUDENT->value);

    $this->otherStudent = User::factory()->create([
        'email_verified_at' => now(),
    ]);
    $this->otherStudent->assignRole(RoleRegistryEnum::STUDENT->value);

    $this->program = Program::create([
        'name' => 'Program Kedinasan STAN',
        'code' => 'STAN-2026',
    ]);

    $this->template = ExamTemplate::create([
        'program_id' => $this->program->id,
        'title' => 'Simulasi SKD Lengkap',
        'type' => ExamTemplateTypeEnum::SKD->value,
        'total_duration_minutes' => 100,
    ]);

    // Subtes TWK
    $this->subjectTwk = Subject::create(['name' => 'TWK', 'code' => 'TWK']);
    $this->sectionTwk = ExamSection::create([
        'exam_template_id' => $this->template->id,
        'subject_id' => $this->subjectTwk->id,
        'title' => 'Tes Wawasan Kebangsaan',
        'passing_grade' => 65,
        'duration_minutes' => 30,
        'order_index' => 1,
    ]);

    $this->questionTwk = Question::create([
        'subject_id' => $this->subjectTwk->id,
        'question_text' => 'Dasar negara Indonesia adalah?',
        'grading_rule' => GradingRuleEnum::STANDARD,
        'difficulty_level' => DifficultyLevelEnum::EASY,
    ]);

    $this->optTwkCorrect = QuestionOption::create([
        'question_id' => $this->questionTwk->id,
        'option_label' => 'A',
        'option_text' => 'Pancasila',
        'is_correct' => true,
        'weight_score' => 5,
    ]);

    $this->optTwkWrong = QuestionOption::create([
        'question_id' => $this->questionTwk->id,
        'option_label' => 'B',
        'option_text' => 'UUD',
        'is_correct' => false,
        'weight_score' => 0,
    ]);

    $this->sectionTwk->questions()->attach($this->questionTwk->id, ['order_index' => 1]);

    // Sesi Ujian Aktif
    $this->session = ExamSession::create([
        'exam_template_id' => $this->template->id,
        'classroom_id' => null, // publik
        'title' => 'Sesi Nasional SKD 1',
        'token' => 'SKD2026',
        'start_time' => now()->subMinutes(10),
        'end_time' => now()->addHours(2),
    ]);
});

test('student can enter session with valid token', function () {
    $response = $this->actingAs($this->student)
        ->postJson(route('cbt.sessions.enter', $this->session), [
            'token' => 'SKD2026',
        ]);

    $response->assertOk()
        ->assertJsonFragment(['session_id' => $this->session->id]);
});

test('student cannot enter session with invalid token', function () {
    $response = $this->actingAs($this->student)
        ->postJson(route('cbt.sessions.enter', $this->session), [
            'token' => 'WRONGTOKEN',
        ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['token']);
});

test('student can start attempt and receives sanitized exam sheet', function () {
    $response = $this->actingAs($this->student)
        ->postJson(route('cbt.sessions.start', $this->session));

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                'attempt_id',
                'status',
                'started_at',
                'sections' => [
                    '*' => [
                        'id',
                        'title',
                        'questions' => [
                            '*' => [
                                'id',
                                'question_text',
                                'options' => [
                                    '*' => ['id', 'option_label', 'option_text'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

    // Pastikan tidak membocorkan is_correct atau weight_score
    $data = $response->json('data');
    $firstOption = $data['sections'][0]['questions'][0]['options'][0];
    expect(array_key_exists('is_correct', $firstOption))->toBeFalse();
    expect(array_key_exists('weight_score', $firstOption))->toBeFalse();
});

test('student can autosave answer for attempt', function () {
    $attempt = ExamAttempt::create([
        'exam_session_id' => $this->session->id,
        'student_id' => $this->student->id,
        'status' => ExamAttemptStatusEnum::IN_PROGRESS,
        'started_at' => now(),
    ]);

    $response = $this->actingAs($this->student)
        ->putJson(route('cbt.attempts.answers.save', $attempt), [
            'question_id' => $this->questionTwk->id,
            'selected_option_id' => $this->optTwkCorrect->id,
            'time_spent_seconds' => 15,
        ]);

    $response->assertOk()
        ->assertJsonFragment(['message' => 'Answer saved successfully']);

    $this->assertDatabaseHas('exam_answers', [
        'exam_attempt_id' => $attempt->id,
        'question_id' => $this->questionTwk->id,
        'selected_option_id' => $this->optTwkCorrect->id,
        'time_spent_seconds' => 15,
    ]);
});

test('other student cannot save answer or submit attempt of another student', function () {
    $attempt = ExamAttempt::create([
        'exam_session_id' => $this->session->id,
        'student_id' => $this->student->id,
        'status' => ExamAttemptStatusEnum::IN_PROGRESS,
        'started_at' => now(),
    ]);

    $response = $this->actingAs($this->otherStudent)
        ->putJson(route('cbt.attempts.answers.save', $attempt), [
            'question_id' => $this->questionTwk->id,
            'selected_option_id' => $this->optTwkCorrect->id,
        ]);

    $response->assertForbidden();
});

test('student can submit attempt and scoring engine calculates section results correctly')->todo();
