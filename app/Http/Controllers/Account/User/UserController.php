<?php

namespace App\Http\Controllers\Account\User;

use App\Actions\Action\Account\User\StoreUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Account\User\StoreUserRequest;
use Illuminate\Http\RedirectResponse;

class UserController extends Controller
{
    public function store(StoreUserRequest $request, StoreUserAction $storeUser): RedirectResponse
    {
        $storeUser->handle($request->validated());
        return to_route('users.index')->with('success', 'users successfully created.');
    }

    public function update(): RedirectResponse
    {
        //
    }
}
