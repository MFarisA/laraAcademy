<?php

namespace App\Actions\Action\Organization\Branch;

use App\Models\Organization\Branch;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteBranchAction
{
    use AsAction;

    public function handle(Branch $branch): bool
    {
        return (bool) $branch->delete();
    }
}
