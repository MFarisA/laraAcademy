<?php

namespace App\Http\Resources\Academic\Batch;

use App\Http\Resources\Academic\Program\ProgramResource;
use App\Models\Academic\Batch;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Batch
 */
class BatchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'program_id' => $this->program_id,
            'name' => $this->name,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'program' => ProgramResource::make($this->whenLoaded('program')),
            'classrooms_count' => $this->whenCounted('classrooms'),
            'created_at' => $this->created_at?->toIsoString(),
        ];
    }
}
