<?php

namespace App\Actions\Action\Academic\Program;

use App\Models\Academic\Program;
use Lorisleiva\Actions\Concerns\AsAction;

class StoreProgramAction
{
    use AsAction;

    public function handle(array $data): Program
    {
        return Program::create($data);
    }
}
