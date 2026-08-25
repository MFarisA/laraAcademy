<?php

namespace App\Models\Assessment\Question;

use Database\Factories\Assessment\Question\QuestionOptionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'question_id',
    'option_label',
    'option_text',
    'is_correct',
    'weight_score',
])]
class QuestionOption extends Model
{
    /** @use HasFactory<QuestionOptionFactory> */
    use HasFactory;

    #[\Override]
    public function casts()
    {
        return [
            'is_correct' => 'boolean',
            'weight_score' => 'decimal:2',
        ];
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
