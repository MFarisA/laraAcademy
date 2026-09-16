<?php

namespace App\Actions\Action\Academic;

use App\Models\Academic\Program;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class SyncProgramSubjectsAction
{
    use AsAction;

    /**
     * @param  array<int, array{id: int, min_passing_score: int|float}>  $subjectsData
     */
    public function handle(Program $program, array $subjectsData): Program
    {
        // Ubah format [{id: 1, min_passing_score: 65}] -> [1 => ['min_passing_score' => 65]]
        $syncData = collect($subjectsData)
            ->mapWithKeys(fn(array $item) => [
                $item['id'] => ['min_passing_score' => $item['min_passing_score']],
            ])
            ->all();

        return DB::transaction(function () use ($program, $syncData) {
            $program->subjects()->sync($syncData);
            return $program->load('subjects');
        });
    }
}
