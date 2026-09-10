<?php

namespace App\Http\Middleware;

use App\Enum\Access\RoleRegistryEnum;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Symfony\Component\HttpFoundation\Response;

class EnsureBranchContext
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user instanceof User) {
            if ($user->branch_id) {
                Context::add('branch_id', $user->branch_id);
            }

            // Admin cabang wajib memiliki cabang yang ditugaskan untuk mengakses data cabang
            if ($user->hasRole(RoleRegistryEnum::ADMINBRANCH->value) && ! $user->branch_id) {
                abort(403, 'Akun admin cabang belum dikaitkan dengan cabang manapun.');
            }
        }

        return $next($request);
    }
}
