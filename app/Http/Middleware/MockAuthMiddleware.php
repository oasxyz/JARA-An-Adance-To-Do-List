<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MockAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Injects a mock/stub user into the request context until Yustinus
     * implements the official authentication middleware.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userId = $request->header('X-User-Id')
            ?? $request->input('user_id')
            ?? 1;

        $userId = (int) $userId;

        $request->setUserResolver(function () use ($userId) {
            $user = User::find($userId);

            if (! $user) {
                $user = new User();
                $user->id = $userId;
                $user->name = 'Mock User '.$userId;
                $user->email = 'mock'.$userId.'@example.com';
            }

            return $user;
        });

        $request->attributes->set('current_user_id', $userId);

        return $next($request);
    }
}

