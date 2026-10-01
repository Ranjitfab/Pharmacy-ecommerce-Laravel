<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse;
use Symfony\Component\HttpFoundation\Response;

class RoleBasedLoginResponse implements LoginResponse
{
    public function toResponse($request): Response
    {
        if ($request->wantsJson()) {
            return response()->json(['two_factor' => false]);
        }

        $destination = $request->user()?->hasRole('user')
            ? route('shop.index')
            : route('dashboard');

        return redirect()->intended($destination);
    }
}
