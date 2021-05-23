<?php

namespace App\Http\Middleware;

use Closure;

class AdminRetail
{
    public function handle($request, Closure $next)
    {
        $user = $request->user();
        if (!$user) return response('Unauthorized', 401);
        if ($user->type != 'admin_retail') return response('Only Admin Retail', 403);
        return $next($request);
    }
}
