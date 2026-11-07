<?php

namespace App\Http\Controllers\Assessment;

use App\Actions\Assessment\Physical\RecordPhysicalAssessmentAction;
use App\Enum\Access\RoleRegistryEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Assessment\Physical\StorePhysicalAssessmentRequest;
use App\Http\Resources\Assessment\Physical\PhysicalAssessmentResource;
use App\Models\Assessment\Physical\PhysicalAssessment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PhysicalAssessmentController extends Controller
{
    public function index(Request $request): Response
    {
        $assessment = PhysicalAssessment::query()
            ->accessibleBy($request->user())
            ->filter($request->only(['search']))
            ->with(['student.branch', 'evaluator:id,name'])
            ->latest('assessment_date')
            ->paginate($request->integer('per_page', 10))
            ->withQueryString();

        return Inertia::render('Assessment/Physical/Index', [
            'assessments' => PhysicalAssessmentResource::collection($assessment),
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(Request $request): Response
    {
        //
    }

    public function store(
        StorePhysicalAssessmentRequest $request,
        RecordPhysicalAssessmentAction $action
    ): RedirectResponse {
        //
    }

    public function show(Request $request, PhysicalAssessment $assessments): Response
    {
        $user = $request->user();
        if ($user->hasRole(RoleRegistryEnum::STUDENT->value)) {
            abort_unless($assessments->student_id === $user->id, 403);
        }
        $assessments->loadMissing([
            'student.branch',
            'evaluator:id,name',
            'testScores',
        ]);

        return Inertia::render('Assessment/Physical/Show', [
            'assessment' => PhysicalAssessmentResource::make($assessments),
        ]);
    }

    public function edit(): Response
    {
        //
    }

    public function update(): RedirectResponse
    {
        //
    }

    public function destroy(): Response
    {
        //
    }
}
