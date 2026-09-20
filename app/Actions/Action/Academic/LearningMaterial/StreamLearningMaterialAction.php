<?php

namespace App\Actions\Action\Academic\LearningMaterial;

use App\Models\Learning\LearningMaterial;
use Illuminate\Support\Facades\Storage;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StreamLearningMaterialAction
{
    use AsAction;

    public function handle(LearningMaterial $material, bool $forceDownload = false): StreamedResponse|BinaryFileResponse
    {
        if (!Storage::disk('local')->exists($material->file_url)) {
            abort(404, 'File materi tidak ditemukan di penyimpanan.');
        }
        $fileName = "{$material->title}.{$material->type}";

        if ($forceDownload) {
            return Storage::disk('local')->download($material->file_url, $fileName);
        }
        return response()->file(
            Storage::disk('local')->path($material->file_url),
            [
                'Content-Disposition' => 'inline; filename="' . $fileName . '"',
            ],
        );
    }
}
