<?php

namespace App\Http\Controllers\Learning;

use App\Http\Controllers\Controller;
use App\Http\Requests\Learning\Schedule\StoreClassScheduleRequest;
use App\Http\Requests\Learning\Schedule\UpdateClassScheduleRequest;
use App\Http\Resources\Learning\Schedule\ClassScheduleResource;
use App\Models\Learning\ClassSchedule;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ClassScheduleController extends Controller
{
    public function index(): Response
    {
        $schedules = ClassSchedule::query()
            ->with(['classroom', 'subject', 'instructor'])
            ->latest('scheduled_at')
            ->paginate(15);

        return Inertia::render('Schedules/Index', [
            'schedules' => ClassScheduleResource::collection($schedules),
        ]);
    }

    public function store(StoreClassScheduleRequest $request): RedirectResponse
    {
        ClassSchedule::create($request->validated());
        return to_route('schedules.index')->with('success', 'schedules successfully created.');
    }

    public function show(ClassSchedule $schedule): Response
    {
        return Inertia::render('Schedules/Show', [
            'schedules' => ClassScheduleResource::make($schedule->load(['classroom', 'subject', 'instructor'])),
        ]);
    }

    public function update(UpdateClassScheduleRequest $request, ClassSchedule $schedule): RedirectResponse
    {
        $schedule->update($request->validated());
        return to_route('schedules.index')->with('success', 'schedules successfully updated.');
    }

    public function destroy(ClassSchedule $schedule): RedirectResponse
    {
        $schedule->delete();
        return to_route('schedules.index')->with('success', 'schedules successfully deleted.');
    }
}
