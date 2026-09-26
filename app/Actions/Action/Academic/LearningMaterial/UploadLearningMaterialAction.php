<?php

namespace App\Actions\Action\Academic\LearningMaterial;

use App\Models\Learning\LearningMaterial;
use Illuminate\Http\UploadedFile;
use Lorisleiva\Actions\Concerns\AsAction;

class UploadLearningMaterialAction
{
    use AsAction;

    /**
     * @param  array<mixed>  $data
     */
    public function handle(UploadedFile $file, array $data): LearningMaterial
    {
        $path = $file->store("materials/{$data['subject_id']}");
        $data['file_url'] = $path;

        return LearningMaterial::create($data);
    }
}
