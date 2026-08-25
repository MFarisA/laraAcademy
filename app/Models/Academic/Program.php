<?php

namespace App\Models\Academic;

use App\Models\Assessment\Exam\ExamTemplate;
use App\Models\Learning\LearningMaterial;
use Database\Factories\Academic\ProgramFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'code',
    'description',
])]
class Program extends Model
{
    /** @use HasFactory<ProgramFactory> */
    use HasFactory;

    /**
     * @return BelongsToMany<Subject, $this>
     */
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class)
            ->using(ProgramSubject::class)
            ->withPivot('min_passing_score');
    }

    /**
     * @return HasMany<Batch, $this>
     */
    public function batches(): HasMany
    {
        return $this->hasMany(Batch::class);
    }

    /**
     * @return HasMany<ExamTemplate, $this>
     */
    public function examTemplates(): HasMany
    {
        return $this->hasMany(ExamTemplate::class);
    }

    /**
     * @return HasMany<LearningMaterial, $this>
     */
    public function learningMaterials(): HasMany
    {
        return $this->hasMany(LearningMaterial::class);
    }
}
