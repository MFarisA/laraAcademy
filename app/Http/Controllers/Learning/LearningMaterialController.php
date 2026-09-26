<?php

namespace App\Http\Controllers\Learning;

use App\Actions\Learning\Material\StreamLearningMaterialAction;
use App\Actions\Learning\Material\UploadLearningMaterialAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Learning\LearningMaterial\StreamLearningMaterialRequest;
use App\Http\Requests\Learning\LearningMaterial\UploadLearningMaterialRequest;
use App\Http\Resources\Learning\LearningMaterial\LearningMaterialResource;
use App\Models\Academic\Subject;
use App\Models\Learning\LearningMaterial;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LearningMaterialController extends Controller
{
    public function index(Subject $subject): Response
    {
        $material = LearningMaterial::query()
            ->where('subject_id', $subject->id)
            ->latest()
            ->paginate(10);

        return Inertia::render('Learning/Material/Index', [
            'material' => LearningMaterialResource::collection($material),
        ]);
    }

    public function store(UploadLearningMaterialRequest $request): RedirectResponse
    {
        UploadLearningMaterialAction::run(
            $request->file('file'),
            $request->validated(),
        );

        return back()->with('success', 'Materi pembelajaran berhasil diunggah');
    }

    public function stream(StreamLearningMaterialRequest $request, LearningMaterial $material): StreamedResponse|BinaryFileResponse
    {
        return StreamLearningMaterialAction::run(
            $material,
            $request->boolean('download')
        );
    }
}
