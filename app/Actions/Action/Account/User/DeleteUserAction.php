<?php

namespace App\Actions\Action\Account\User;

use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteUserAction
{
    use AsAction;

    public function handle(User $user): bool
    {
        return (bool) $user->delete();
    }
}
