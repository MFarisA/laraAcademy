<?php

use App\Enum\Assessment\GradingRuleEnum;
use App\Models\Academic\Subject;
use App\Models\Assessment\Question\Question;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::findOrCreate('super-admin');
    $this->superAdmin = User::factory()->create([
        'email_verified_at' => now(),
    ]);
    $this->superAdmin->assignRole('super-admin');

    $this->subject = Subject::create([
        'name' => 'Tes Wawasan Kebangsaan',
        'code' => 'TWK',
        'description' => 'Materi TWK SKD',
    ]);
});

test('super admin can view questions list', function () {
    Question::create([
        'subject_id' => $this->subject->id,
        'question_text' => 'Siapakah presiden pertama RI?',
        'grading_rule' => GradingRuleEnum::STANDARD,
        'difficulty_level' => 'easy',
    ]);

    $this->withoutVite();

    $response = $this->actingAs($this->superAdmin)
        ->get(route('questions.index'));

    $response->assertOk();
});

test('super admin can view a question detail', function () {
    $question = Question::create([
        'subject_id' => $this->subject->id,
        'question_text' => 'Soal untuk ditampilkan',
        'grading_rule' => GradingRuleEnum::STANDARD,
        'difficulty_level' => 'easy',
    ]);

    $this->withoutVite();

    $response = $this->actingAs($this->superAdmin)
        ->get(route('questions.show', $question));

    $response->assertOk();
});

test('super admin can create standard question with valid options', function () {
    $payload = [
        'subject_id' => $this->subject->id,
        'question_text' => 'Berapa hasil dari 5 + 5?',
        'grading_rule' => GradingRuleEnum::STANDARD->value,
        'difficulty_level' => 'easy',
        'options' => [
            [
                'option_label' => 'A',
                'option_text' => '10',
                'is_correct' => true,
                'weight_score' => 5,
            ],
            [
                'option_label' => 'B',
                'option_text' => '9',
                'is_correct' => false,
                'weight_score' => 0,
            ],
        ],
    ];

    $response = $this->actingAs($this->superAdmin)
        ->post(route('questions.store'), $payload);

    $response->assertSessionHas('success');

    $this->assertDatabaseHas('questions', [
        'subject_id' => $this->subject->id,
        'question_text' => 'Berapa hasil dari 5 + 5?',
        'grading_rule' => GradingRuleEnum::STANDARD->value,
    ]);

    $question = Question::first();
    expect($question)->not->toBeNull();
    expect($question->options)->toHaveCount(2);

    $correctOption = $question->options->where('is_correct', true)->first();
    expect($correctOption)->not->toBeNull();
    expect($correctOption->option_label)->toBe('A');
    expect((float) $correctOption->weight_score)->toBe(5.0);
});

test('creating standard question fails if no option is marked as correct', function () {
    $payload = [
        'subject_id' => $this->subject->id,
        'question_text' => 'Pertanyaan tanpa kunci jawaban?',
        'grading_rule' => GradingRuleEnum::STANDARD->value,
        'difficulty_level' => 'medium',
        'options' => [
            [
                'option_label' => 'A',
                'option_text' => 'Opsi 1',
                'is_correct' => false,
            ],
            [
                'option_label' => 'B',
                'option_text' => 'Opsi 2',
                'is_correct' => false,
            ],
        ],
    ];

    $response = $this->actingAs($this->superAdmin)
        ->post(route('questions.store'), $payload);

    $response->assertSessionHasErrors(['options']);
    $this->assertDatabaseMissing('questions', [
        'question_text' => 'Pertanyaan tanpa kunci jawaban?',
    ]);
});

test('super admin can create tkp question where all options have weights 1 to 5', function () {
    $payload = [
        'subject_id' => $this->subject->id,
        'question_text' => 'Bagaimana respon Anda saat rekan kerja membuat kesalahan?',
        'grading_rule' => GradingRuleEnum::TKP->value,
        'difficulty_level' => 'hard',
        'options' => [
            [
                'option_label' => 'A',
                'option_text' => 'Membantu memperbaikinya bersama',
                'weight_score' => 5,
            ],
            [
                'option_label' => 'B',
                'option_text' => 'Melaporkannya ke atasan',
                'weight_score' => 3,
            ],
            [
                'option_label' => 'C',
                'option_text' => 'Mengabaikannya',
                'weight_score' => 1,
            ],
        ],
    ];

    $response = $this->actingAs($this->superAdmin)
        ->post(route('questions.store'), $payload);

    $response->assertSessionHas('success');

    $question = Question::where('question_text', $payload['question_text'])->first();
    expect($question)->not->toBeNull();
    expect($question->grading_rule)->toBe(GradingRuleEnum::TKP);
    expect($question->options)->toHaveCount(3);

    // Pada TKP, is_correct harus bernilai false untuk semua opsi
    expect($question->options->where('is_correct', true))->toHaveCount(0);
});

test('creating tkp question fails if weight_score is out of range 1 to 5', function () {
    $payload = [
        'subject_id' => $this->subject->id,
        'question_text' => 'Soal TKP dengan bobot tidak valid',
        'grading_rule' => GradingRuleEnum::TKP->value,
        'difficulty_level' => 'medium',
        'options' => [
            [
                'option_label' => 'A',
                'option_text' => 'Opsi bobot 6',
                'weight_score' => 6, // Tidak valid (harus 1-5)
            ],
            [
                'option_label' => 'B',
                'option_text' => 'Opsi bobot 0',
                'weight_score' => 0, // Tidak valid (harus 1-5)
            ],
        ],
    ];

    $response = $this->actingAs($this->superAdmin)
        ->post(route('questions.store'), $payload);

    $response->assertSessionHasErrors([
        'options.0.weight_score',
        'options.1.weight_score',
    ]);
});

test('super admin can update question and replace its options', function () {
    $question = Question::create([
        'subject_id' => $this->subject->id,
        'question_text' => 'Teks lama',
        'grading_rule' => GradingRuleEnum::STANDARD,
        'difficulty_level' => 'easy',
    ]);

    $question->options()->create([
        'option_label' => 'A',
        'option_text' => 'Opsi lama',
        'is_correct' => true,
        'weight_score' => 1,
    ]);

    $updatePayload = [
        'subject_id' => $this->subject->id,
        'question_text' => 'Teks baru yang sudah diedit',
        'difficulty_level' => 'medium',
        'options' => [
            [
                'option_label' => 'A',
                'option_text' => 'Opsi baru A',
                'is_correct' => false,
                'weight_score' => 0,
            ],
            [
                'option_label' => 'B',
                'option_text' => 'Opsi baru B (Kunci)',
                'is_correct' => true,
                'weight_score' => 5,
            ],
        ],
    ];

    $response = $this->actingAs($this->superAdmin)
        ->put(route('questions.update', $question), $updatePayload);

    $response->assertSessionHas('success');

    $question->refresh();
    expect($question->question_text)->toBe('Teks baru yang sudah diedit');
    expect($question->difficulty_level)->toBe('medium');
    expect($question->options)->toHaveCount(2);

    $newCorrect = $question->options->where('is_correct', true)->first();
    expect($newCorrect->option_label)->toBe('B');
});

test('super admin can soft delete a question', function () {
    $question = Question::create([
        'subject_id' => $this->subject->id,
        'question_text' => 'Soal yang akan dihapus',
        'grading_rule' => GradingRuleEnum::STANDARD,
        'difficulty_level' => 'easy',
    ]);

    $response = $this->actingAs($this->superAdmin)
        ->delete(route('questions.destroy', $question));

    $response->assertSessionHas('success');

    // Pastikan terhapus secara soft delete (masih ada di withTrashed)
    $this->assertSoftDeleted('questions', [
        'id' => $question->id,
    ]);
});
