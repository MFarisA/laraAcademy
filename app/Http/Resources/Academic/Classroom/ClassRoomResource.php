<?php

namespace App\Http\Resources\Academic\Classroom;

use App\Http\Resources\Academic\Batch\BatchResource;
use App\Http\Resources\Organization\Branch\BranchResource;
use App\Models\Academic\Classroom;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Classroom
 */
class ClassRoomResource extends JsonResource
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
            'batch_id' => $this->batch_id,
            'branch_id' => $this->branch_id,
            'name' => $this->name,
            'capacity' => $this->capacity,

            'students_count' => $this->whenCounted('students'),
            'branch' => BranchResource::make($this->whenLoaded('branch')),
            'batch' => BatchResource::make($this->whenLoaded('batch')),

            'created_at' => $this->created_at,
        ];
    }
}
