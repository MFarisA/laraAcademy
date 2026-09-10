<?php

namespace App\Http\Responses;

use App\Enum\Access\RoleRegistryEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Symfony\Component\HttpFoundation\Response;

class LoginResponse implements LoginResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  Request  $request
     */
    public function toResponse($request): Response
    {
        if ($request->wantsJson()) {
            return response()->json(['two_factor' => false]);
        }

        $user = $request->user();

        $targetRoute = match (true) {
            $user?->hasAnyRole([
                RoleRegistryEnum::SUPERADMIN->value,
                RoleRegistryEnum::ADMINBRANCH->value,
            ]) => 'admin.dashboard',
            $user?->hasRole(RoleRegistryEnum::INSTRUCTOR->value) => 'schedules.index',
            default => 'dashboard',
        };

        $redirectUrl = Route::has($targetRoute) ? route($targetRoute) : route('dashboard');

        return redirect()->intended($redirectUrl);
    }
}
