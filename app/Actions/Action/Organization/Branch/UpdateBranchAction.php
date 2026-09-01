<?php

namespace App\Actions\Action\Organization\Branch;

use App\Models\Organization\Branch;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateBranchAction
{
    use AsAction;

    /**
     * @param array<string, mixed> $data
     */
    public function handle(Branch $branch, array $data): Branch
    {
        $branch->update($data);
        return $branch;
    }
}
