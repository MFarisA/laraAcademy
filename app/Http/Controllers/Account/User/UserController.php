<?php

namespace App\Http\Controllers\Account\User;

use App\Actions\Action\Account\User\StoreUserAction;
use App\Actions\Action\Account\User\UpdateUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Account\User\StoreUserRequest;
use App\Http\Requests\Account\User\UpdateUserRequest;
use App\Http\Resources\Account\User\UserResource;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Users/Index', [
            'users' => UserResource::collection(
                User::query()
                    ->latest()
                    ->get()
            ),
        ]);
    }
    public function store(StoreUserRequest $request, StoreUserAction $storeUser): RedirectResponse
    {
        $storeUser->handle($request->validated());
        return to_route('users.index')->with('success', 'users successfully created.');
    }

    public function update(UpdateUserRequest $request, User $user, UpdateUserAction $updateUser): RedirectResponse
    {
        $data = $request->validated();
        $updateUser->handle($user, $data);
        return to_route('users.index')->with('success', 'users successfully updated.');
    }
}
