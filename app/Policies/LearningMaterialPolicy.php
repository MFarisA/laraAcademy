<?php

namespace App\Policies;

use App\Enum\Access\RoleRegistryEnum;
use App\Models\Learning\LearningMaterial;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class LearningMaterialPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Verifikasi akses: user harus terdaftar aktif pada subjek/program terkait.
     */
    public function view(User $user, LearningMaterial $material): bool
    {
        if ($user->hasAnyRole([
            RoleRegistryEnum::ADMINBRANCH->value,
            RoleRegistryEnum::INSTRUCTOR->value,
        ])) {
            return true;
        }
        // Cek apakah siswa memiliki enrollment aktif di kelas yang mempelajari subject ini
        return $user->enrollments()
            ->where('status', 'active')
            ->whereHas('classroom', function (Builder $query) use ($material) {
                $query->whereHas('batch', function (Builder $batchQuery) use ($material) {
                    if ($material->program_id !== null) {
                        $batchQuery->where('program_id', $material->program_id);
                    }
                    $batchQuery->whereHas('program', function (Builder $programQuery) use ($material) {
                        $programQuery->whereHas('subjects', function (Builder $subjectQuery) use ($material) {
                            $subjectQuery->where('subjects.id', $material->subject_id);
                        });
                    });
                });
            })
            ->exists();
    }

    /**
     * Verifikasi download: wajib punya hak view dan materi diizinkan diunduh.
     */
    public function download(User $user, LearningMaterial $material): bool
    {
        // Wajib lolos hak akses view terlebih dahulu
        if (! $this->view($user, $material)) {
            return false;
        }

        // Staf cabang & instruktur bebas mengunduh file
        if ($user->hasAnyRole([
            RoleRegistryEnum::INSTRUCTOR->value,
            RoleRegistryEnum::ADMINBRANCH->value,
        ])) {
            return true;
        }

        // Untuk student: patuhi flag is_downloadable
        return $material->is_downloadable;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole([
            RoleRegistryEnum::ADMINBRANCH->value,
            RoleRegistryEnum::INSTRUCTOR->value,
        ]);
    }

    /**
     * @return false
     */
    public function update(User $user, LearningMaterial $learningMaterial): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LearningMaterial $learningMaterial): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, LearningMaterial $learningMaterial): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, LearningMaterial $learningMaterial): bool
    {
        return false;
    }
}
