<?php

use App\Enum\Access\RoleRegistryEnum;
use App\Enum\Assessment\DifficultyLevelEnum;
use App\Enum\Assessment\GradingRuleEnum;
use App\Models\Academic\Subject;
use App\Models\Assessment\Question\Question;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::findOrCreate(RoleRegistryEnum::SUPERADMIN->value);
    $this->user = User::factory()->create();
    $this->user->assignRole(RoleRegistryEnum::SUPERADMIN->value);
    $this->actingAs($this->user);
});

test('dapat memfilter pertanyaan berdasarkan subject_id yang valid', function () {
    $subjectA = Subject::factory()->create();
    $subjectB = Subject::factory()->create();

    $qA = Question::factory()->create([
        'subject_id' => $subjectA->id,
        'question_text' => 'Pertanyaan spesifik mata pelajaran A',
    ]);
    Question::factory()->create([
        'subject_id' => $subjectB->id,
        'question_text' => 'Pertanyaan spesifik mata pelajaran B',
    ]);

    // Kirim ID sebagai string seperti yang dihasilkan query param URL
    $response = $this->get(route('questions.index', [
        'subject_id' => (string) $subjectA->id,
    ]));

    $response->assertOk();
    $response->assertInertia(
        fn ($page) => $page
            ->component('Academic/Assessment/Index')
            ->has('questions.data', 1)
            ->where('questions.data.0.id', $qA->id)
            ->has('subjects')
            ->where('filters.subject_id', (string) $subjectA->id)
    );
});

test('dapat memfilter pertanyaan berdasarkan difficulty_level', function () {
    $subject = Subject::factory()->create();

    $qHard = Question::factory()->create([
        'subject_id' => $subject->id,
        'difficulty_level' => DifficultyLevelEnum::HARD->value,
    ]);
    Question::factory()->create([
        'subject_id' => $subject->id,
        'difficulty_level' => DifficultyLevelEnum::EASY->value,
    ]);

    $response = $this->get(route('questions.index', [
        'difficulty_level' => 'hard',
    ]));

    $response->assertOk();
    $response->assertInertia(
        fn ($page) => $page
            ->has('questions.data', 1)
            ->where('questions.data.0.id', $qHard->id)
            ->where('filters.difficulty_level', 'hard')
    );
});

test('dapat memfilter pertanyaan berdasarkan grading_rule', function () {
    $subject = Subject::factory()->create();

    $qTkp = Question::factory()->create([
        'subject_id' => $subject->id,
        'grading_rule' => GradingRuleEnum::TKP,
    ]);
    Question::factory()->create([
        'subject_id' => $subject->id,
        'grading_rule' => GradingRuleEnum::STANDARD,
    ]);

    $response = $this->get(route('questions.index', [
        'grading_rule' => GradingRuleEnum::TKP->value,
    ]));

    $response->assertOk();
    $response->assertInertia(
        fn ($page) => $page
            ->has('questions.data', 1)
            ->where('questions.data.0.id', $qTkp->id)
            ->where('filters.grading_rule', GradingRuleEnum::TKP->value)
    );
});

test('dapat mencari pertanyaan dengan full-text search postgresql yang cocok', function () {
    $subject = Subject::factory()->create();

    $qHit = Question::factory()->create([
        'subject_id' => $subject->id,
        'question_text' => 'Amandemen Undang-Undang Dasar Republik Indonesia tahun 1945 dilakukan bertahap.',
    ]);
    Question::factory()->create([
        'subject_id' => $subject->id,
        'question_text' => 'Berapa luas segitiga sama sisi jika sisi adalah sepuluh sentimeter?',
    ]);

    $response = $this->get(route('questions.index', [
        'search' => 'Undang-Undang',
    ]));

    $response->assertOk();
    $response->assertInertia(
        fn ($page) => $page
            ->has('questions.data', 1)
            ->where('questions.data.0.id', $qHit->id)
            ->where('filters.search', 'Undang-Undang')
    );
});

test('pencarian full-text search yang tidak cocok menghasilkan list kosong', function () {
    $subject = Subject::factory()->create();

    Question::factory()->create([
        'subject_id' => $subject->id,
        'question_text' => 'Sila kelima Pancasila menekankan keadilan sosial bagi seluruh rakyat.',
    ]);

    $response = $this->get(route('questions.index', [
        'search' => 'trigonometri',
    ]));

    $response->assertOk();
    $response->assertInertia(
        fn ($page) => $page
            ->has('questions.data', 0)
    );
});

test('parameter filter yang tidak valid ditolak dengan status 422 saat request JSON', function () {
    $response = $this->getJson(route('questions.index', [
        'difficulty_level' => 'super_hard',
        'grading_rule' => 'INVALID_RULE',
        'subject_id' => 999999,
    ]));

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['difficulty_level', 'grading_rule', 'subject_id']);
});
