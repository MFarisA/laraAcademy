<?php

namespace App\Concerns;

use App\Enum\Access\RoleRegistryEnum;
use App\Models\Organization\Branch;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Context;

trait BelongsToBranch
{
    /**
     * @return BelongsTo<Branch, $this>
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Scope a query to only include records accessible by the given user/branch context.
     *
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeAccessibleBy(Builder $query, ?User $user = null): Builder
    {
        $user ??= Auth::user();

        if (! $user instanceof User || $user->hasRole(RoleRegistryEnum::SUPERADMIN->value)) {
            return $query;
        }

        $branchId = $user->branch_id ?? Context::get('branch_id');

        return $query->where($this->qualifyColumn('branch_id'), $branchId);
    }
}
