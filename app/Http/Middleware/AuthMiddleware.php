<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $request->header('Authorization');
        if (is_null($token) || !Redis::exists(TUJIA_TOKEN_PREFIX . $token)) {
            return responseTo(false,'授权已失效，请重新登录',10006);
        }else{
            Redis::expire(TUJIA_TOKEN_PREFIX . $token, TUJIA_EXPIRE_TIME);
        }
        return $next($request);
    }
}
