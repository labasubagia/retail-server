<?php

namespace App\Http\Middleware;

use Closure;

class AdminStore
{
    public function handle($request, Closure $next)
    {
        $user = $request->user();
        if (!$user) return response('Unauthorized', 401);
        if ($user->type != 'admin_store') return response('Only Admin Store', 403);
        return $next($request);
    }
}
