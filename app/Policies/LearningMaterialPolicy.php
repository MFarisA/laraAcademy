<?php

namespace App\Policies;

use App\Models\Learning\LearningMaterial;
use App\Models\User;

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
        if ($user->hasRole('admin')) {
            return true;
        }

        // Cek apakah siswa memiliki enrollment aktif di kelas yang mempelajari subject ini
        return $user->enrollments()
            ->where('status', 'active')
            ->whereHas('classroom', function ($query) use ($material) {
                $query->where('subject_id', $material->subject_id);

                if ($material->program_id !== null) {
                    $query->where('program_id', $material->program_id);
                }
            })
            ->exists();
    }

    /**
     * Verifikasi download: wajib punya hak view dan materi diizinkan diunduh.
     */
    public function download(User $user, LearningMaterial $material): bool
    {
        if (!$this->view($user, $material)) {
            return false;
        }

        if (!$user->hasRole('admin') && !$material->is_downloadable) {
            return false;
        }
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
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
