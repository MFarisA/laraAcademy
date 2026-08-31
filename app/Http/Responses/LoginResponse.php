<?php

namespace App\Http\Responses;

use App\Enum\Access\RoleRegistryEnum;
use Illuminate\Http\Request;
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

        $targetUrl = match (true) {
            $user->hasAnyRole([RoleRegistryEnum::SUPERADMIN->value, RoleRegistryEnum::ADMINBRANCH->value]) => route('dashboard'),

            $user->hasRole(RoleRegistryEnum::INSTRUCTOR->value) => route('dashboard'),

            default => route('dashboard'),
        };

        return redirect()->intended($targetUrl);
    }
}
