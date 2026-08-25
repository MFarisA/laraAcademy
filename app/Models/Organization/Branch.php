<?php

namespace App\Models\Organization;

use App\Models\Academic\Classroom;
use App\Models\User;
use Database\Factories\Organization\BranchFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'code',
    'city',
    'address',
])]
class Branch extends Model
{
    /** @use HasFactory<BranchFactory> */
    use HasFactory;

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function classrooms(): HasMany
    {
        return $this->hasMany(Classroom::class);
    }
}
