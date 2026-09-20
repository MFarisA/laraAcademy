<?php

namespace App\Http\Resources\Learning\LearningMaterial;

use App\Models\Learning\LearningMaterial;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

/**
 * @mixin LearningMaterial
 */
class LearningMaterialResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $isExternalLink = $this->type === 'link';
        return [
            'subject_id' => $this->subject_id,
            'program_id' => $this->program_id,
            'title' => $this->title,
            'type' => $this->type,
            'file_url' => $this->file_url,
            'is_downloadable' => $this->is_downloadable,

            'preview_url' => $isExternalLink
                ? $this->file_url
                : URL::temporarySignedRoute(
                    'materials.stream',
                    now()->addMinutes(30),
                    ['material' => $this->id]
                ),
            'download_url' => (! $isExternalLink && $this->is_downloadable)
                ? URL::temporarySignedRoute(
                    'materials.stream',
                    now()->addMinutes(30),
                    ['material' => $this->id, 'download', 1],
                ) : null,
            'created_at' => $this->created_at?->toIsoString(),
        ];
    }
}
