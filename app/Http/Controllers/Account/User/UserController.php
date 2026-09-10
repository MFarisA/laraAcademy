<?php

namespace App\Http\Controllers\Account\User;

use App\Actions\Action\Account\User\DeleteUserAction;
use App\Actions\Action\Account\User\StoreUserAction;
use App\Actions\Action\Account\User\UpdateUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Account\User\StoreUserRequest;
use App\Http\Requests\Account\User\UpdateUserRequest;
use App\Http\Resources\Account\User\UserResource;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', User::class);
        $users = User::query()
            ->accessibleBy($request->user())
            ->with(['branch', 'roles'])
            ->filter($request->only(['search', 'is_active', 'branch_id', 'roles']))
            ->latest()
            ->paginate($request->integer('per_page', 15))
            ->withQueryString();

        return Inertia::render('Users/Index', [
            'users' => UserResource::collection($users),
            'filters' => $request->only(['search', 'branch_id', 'is_active', 'roles']),
        ]);
    }

    public function store(StoreUserRequest $request, StoreUserAction $storeUser): RedirectResponse
    {
        $storeUser->handle($request->validated());

        return to_route('users.index')->with('success', 'users successfully created.');
    }

    public function show(User $user): Response
    {
        return Inertia::render('Users/Show', [
            'Users' => new UserResource($user),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user, UpdateUserAction $updateUser): RedirectResponse
    {
        $data = $request->validated();
        $updateUser->handle($user, $data);

        return to_route('users.index')->with('success', 'users successfully updated.');
    }

    public function destroy(User $user, DeleteUserAction $deleteUser): RedirectResponse
    {
        $deleteUser->handle($user);
        session()->flash('success', 'Branch successfully deleted.');

        return to_route('users.index');
    }
}
