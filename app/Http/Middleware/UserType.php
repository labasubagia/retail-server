<?php

namespace App\Http\Middleware;

use Closure;

class UserType
{
    public function handle($request, Closure $next, $type)
    {
        $user = $request->user();
        if (!$user) return response('Unauthorized', 401);
        if ($user->type != $type) return response("only $type user", 403);
        return $next($request);
    }
}
