<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\RegisterResponse;
use Symfony\Component\HttpFoundation\Response;

class RoleBasedRegisterResponse implements RegisterResponse
{
    public function toResponse($request): Response
    {
        if ($request->wantsJson()) {
            return new JsonResponse('', 201);
        }

        $destination = $request->user()?->hasRole('user')
            ? route('shop.index')
            : route('dashboard');

        return redirect()->intended($destination);
    }
}
