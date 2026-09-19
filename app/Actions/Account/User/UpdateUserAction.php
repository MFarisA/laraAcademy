<?php

namespace App\Actions\Account\User;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateUserAction
{
    use AsAction;

    /**
     * @param  array<mixed>  $data
     */
    public function handle(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            $role = $data['roles'] ?? null;
            unset($data['roles']);

            if (empty($data['password'])) {
                unset($data['password']);
            }
            $user->update($data);
            if ($role !== null) {
                $user->syncRoles($role);
            }

            return $user;
        });
    }
}
