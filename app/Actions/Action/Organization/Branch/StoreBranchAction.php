<?php

namespace App\Actions\Action\Organization\Branch;

use App\Models\Organization\Branch;
use Lorisleiva\Actions\Concerns\AsAction;

class StoreBranchAction
{
    use AsAction;

    /**
     * @param array<mixed> $data
     */
    public function handle(array $data): Branch
    {
        return Branch::create($data);
    }
}
