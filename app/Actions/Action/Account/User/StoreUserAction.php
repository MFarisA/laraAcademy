<?php

namespace App\Actions\Action\Account\User;

use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;

class StoreUserAction
{
    use AsAction;

    /**
     * @param array<mixed> $data
     */
    public function handle(array $data): User
    {
        $role = $data['roles'] ?? null;
        unset($data['roles']);

        $user = User::create($data);
        if (! empty($role)) {
            $user->assignRole($role);
        }
        return $user;
    }
}
