<?php

namespace App\Http\Middleware;

use Closure;

class Customer
{
    public function handle($request, Closure $next)
    {
        $user = $request->user();
        if (!$user) return response('Unauthorized', 401);
        if ($user->type != 'customer') return response('Only Customer', 403);
        return $next($request);
    }
}
