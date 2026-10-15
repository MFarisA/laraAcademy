<?php

use App\Actions\Assessment\Cbt\CalculateSessionRanksAction;
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
use App\Models\Organization\Branch;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
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

    // Subtes TWK (Standard Rule)
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

    // Subtes TKP (TKP Rule)
    $this->subjectTkp = Subject::create(['name' => 'TKP', 'code' => 'TKP']);
    $this->sectionTkp = ExamSection::create([
        'exam_template_id' => $this->template->id,
        'subject_id' => $this->subjectTkp->id,
        'title' => 'Tes Karakteristik Pribadi',
        'passing_grade' => 150,
        'duration_minutes' => 35,
        'order_index' => 2,
    ]);

    $this->questionTkp = Question::create([
        'subject_id' => $this->subjectTkp->id,
        'question_text' => 'Saat rekan tim terlambat, respon Anda adalah?',
        'grading_rule' => GradingRuleEnum::TKP,
        'difficulty_level' => DifficultyLevelEnum::MEDIUM,
    ]);

    $this->optTkpFive = QuestionOption::create([
        'question_id' => $this->questionTkp->id,
        'option_label' => 'A',
        'option_text' => 'Menghubungi dan membagi beban kerja',
        'is_correct' => false,
        'weight_score' => 5,
    ]);

    $this->optTkpThree = QuestionOption::create([
        'question_id' => $this->questionTkp->id,
        'option_label' => 'B',
        'option_text' => 'Menunggu hingga datang',
        'is_correct' => false,
        'weight_score' => 3,
    ]);

    $this->sectionTkp->questions()->attach($this->questionTkp->id, ['order_index' => 1]);

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

    $submitResponse = $this->actingAs($this->otherStudent)
        ->postJson(route('cbt.attempts.submit', $attempt));

    $submitResponse->assertForbidden();
});

test('student can submit attempt and scoring engine calculates section results correctly', function () {
    $attempt = ExamAttempt::create([
        'exam_session_id' => $this->session->id,
        'student_id' => $this->student->id,
        'status' => ExamAttemptStatusEnum::IN_PROGRESS,
        'started_at' => now(),
    ]);

    // TWK: Jawab Benar (Bobot 5)
    $this->actingAs($this->student)
        ->putJson(route('cbt.attempts.answers.save', $attempt), [
            'question_id' => $this->questionTwk->id,
            'selected_option_id' => $this->optTwkCorrect->id,
            'time_spent_seconds' => 20,
        ]);

    // TKP: Jawab Opsi A (Bobot 5)
    $this->actingAs($this->student)
        ->putJson(route('cbt.attempts.answers.save', $attempt), [
            'question_id' => $this->questionTkp->id,
            'selected_option_id' => $this->optTkpFive->id,
            'time_spent_seconds' => 25,
        ]);

    // Submit ujian
    $response = $this->actingAs($this->student)
        ->postJson(route('cbt.attempts.submit', $attempt));

    $response->assertOk()
        ->assertJsonFragment([
            'total_score' => 10.0,
            'status' => ExamAttemptStatusEnum::COMPLETED->value,
        ]);

    $attempt->refresh();
    expect($attempt->status)->toBe(ExamAttemptStatusEnum::COMPLETED);
    expect((float) $attempt->total_score)->toBe(10.0);
    expect($attempt->submitted_at)->not->toBeNull();

    // Verifikasi tabel exam_section_results terisi otomatis
    $this->assertDatabaseHas('exam_section_results', [
        'exam_attempt_id' => $attempt->id,
        'exam_section_id' => $this->sectionTwk->id,
        'score' => 5.0,
    ]);

    $this->assertDatabaseHas('exam_section_results', [
        'exam_attempt_id' => $attempt->id,
        'exam_section_id' => $this->sectionTkp->id,
        'score' => 5.0,
    ]);
});

test('ranking engine calculates national and branch rank with deterministic tie-breaking', function () {
    $branchA = Branch::factory()->create(['name' => 'Cabang Jakarta']);
    $branchB = Branch::factory()->create(['name' => 'Cabang Surabaya']);

    $student1 = User::factory()->create(['branch_id' => $branchA->id]);
    $student2 = User::factory()->create(['branch_id' => $branchA->id]);
    $student3 = User::factory()->create(['branch_id' => $branchA->id]);
    $student4 = User::factory()->create(['branch_id' => $branchB->id]);

    // Student 1: Skor 90, durasi 30 menit (Rank 1 Nasional, Rank 1 Cabang A)
    $attempt1 = ExamAttempt::create([
        'exam_session_id' => $this->session->id,
        'student_id' => $student1->id,
        'status' => ExamAttemptStatusEnum::COMPLETED,
        'total_score' => 90.00,
        'started_at' => now()->subMinutes(30),
        'submitted_at' => now(),
    ]);

    // Student 4: Skor 85, durasi 20 menit (Rank 2 Nasional, Rank 1 Cabang B)
    $attempt4 = ExamAttempt::create([
        'exam_session_id' => $this->session->id,
        'student_id' => $student4->id,
        'status' => ExamAttemptStatusEnum::COMPLETED,
        'total_score' => 85.00,
        'started_at' => now()->subMinutes(20),
        'submitted_at' => now(),
    ]);

    // Student 2: Skor 80, durasi 15 menit (lebih cepat dari Student 3 => Rank 3 Nasional, Rank 2 Cabang A)
    $attempt2 = ExamAttempt::create([
        'exam_session_id' => $this->session->id,
        'student_id' => $student2->id,
        'status' => ExamAttemptStatusEnum::COMPLETED,
        'total_score' => 80.00,
        'started_at' => now()->subMinutes(15),
        'submitted_at' => now(),
    ]);

    // Student 3: Skor 80, durasi 25 menit (lebih lambat dari Student 2 => Rank 4 Nasional, Rank 3 Cabang A)
    $attempt3 = ExamAttempt::create([
        'exam_session_id' => $this->session->id,
        'student_id' => $student3->id,
        'status' => ExamAttemptStatusEnum::COMPLETED,
        'total_score' => 80.00,
        'started_at' => now()->subMinutes(25),
        'submitted_at' => now(),
    ]);

    // Jalankan kalkulasi ranking
    CalculateSessionRanksAction::run($this->session->id);

    $attempt1->refresh();
    $attempt2->refresh();
    $attempt3->refresh();
    $attempt4->refresh();

    // Verifikasi Ranking Nasional
    expect($attempt1->national_rank)->toBe(1);
    expect($attempt4->national_rank)->toBe(2);
    expect($attempt2->national_rank)->toBe(3);
    expect($attempt3->national_rank)->toBe(4);

    // Verifikasi Ranking Cabang
    expect($attempt1->branch_rank)->toBe(1);
    expect($attempt2->branch_rank)->toBe(2);
    expect($attempt3->branch_rank)->toBe(3);
    expect($attempt4->branch_rank)->toBe(1);
});

test('student can view own exam result but cannot view others unless super admin', function () {
    $attempt = ExamAttempt::create([
        'exam_session_id' => $this->session->id,
        'student_id' => $this->student->id,
        'status' => ExamAttemptStatusEnum::COMPLETED,
        'total_score' => 85.00,
        'is_passed' => true,
        'started_at' => now()->subMinutes(30),
        'submitted_at' => now(),
    ]);

    // Siswa pemilik attempt -> 200 OK
    $this->actingAs($this->student)
        ->get(route('cbt.attempts.result', $attempt))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Assessment/Cbt/Result')
            ->has('attempt')
            ->has('session')
        );

    // Siswa lain mencoba melihat -> 403 Forbidden
    $this->actingAs($this->otherStudent)
        ->get(route('cbt.attempts.result', $attempt))
        ->assertForbidden();

    // Super Admin mencoba melihat -> 200 OK
    $admin = User::factory()->create();
    $admin->assignRole(RoleRegistryEnum::SUPERADMIN->value);

    $this->actingAs($admin)
        ->get(route('cbt.attempts.result', $attempt))
        ->assertOk();
});

test('student can view session leaderboard with national and branch scope', function () {
    $branch = Branch::factory()->create();
    $this->student->update(['branch_id' => $branch->id]);

    ExamAttempt::create([
        'exam_session_id' => $this->session->id,
        'student_id' => $this->student->id,
        'status' => ExamAttemptStatusEnum::COMPLETED,
        'total_score' => 90.00,
        'national_rank' => 1,
        'branch_rank' => 1,
        'is_passed' => true,
        'started_at' => now()->subMinutes(20),
        'submitted_at' => now(),
    ]);

    // Leaderboard scope nasional
    $this->actingAs($this->student)
        ->get(route('cbt.sessions.leaderboard', $this->session))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Assessment/Cbt/Leaderboard')
            ->has('session')
            ->has('attempts.data', 1)
            ->where('currentScope', 'national')
        );

    // Leaderboard scope cabang
    $this->actingAs($this->student)
        ->get(route('cbt.sessions.leaderboard', ['exam_session' => $this->session, 'scope' => 'branch']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Assessment/Cbt/Leaderboard')
            ->where('currentScope', 'branch')
        );
});

test('submitting attempt redirects to exam result page and triggers ranking calculation', function () {
    $attempt = ExamAttempt::create([
        'exam_session_id' => $this->session->id,
        'student_id' => $this->student->id,
        'status' => ExamAttemptStatusEnum::IN_PROGRESS,
        'total_score' => 0.00,
        'started_at' => now()->subMinutes(15),
    ]);

    $response = $this->actingAs($this->student)
        ->post(route('cbt.attempts.submit', $attempt));

    $response->assertRedirect(route('cbt.attempts.result', $attempt));

    $attempt->refresh();
    expect($attempt->status)->toBe(ExamAttemptStatusEnum::COMPLETED);
    expect($attempt->national_rank)->not->toBeNull();
});
