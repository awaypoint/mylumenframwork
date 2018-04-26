<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Support\Facades\Session;

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        //不再做token验证
        //if ($this->auth->guard($guard)->guest() && !env('APP_DEBUG')) {
        //return responseTo('授权失败', '授权失败', 401);
        //}
        $nowTime = time();
        if (env('APP_DEBUG')) {
            Session::put('uid', 59);
            Session::put('lifetime', $nowTime + env('LIFE_SEC', 10800));
        }
        if (!Session::has('lifetime') || Session::get('lifetime') - $nowTime < 0) {
            Session::flush();
            return responseTo('', '您长时间未操作，登录已过期！请重新登录', 401);
        }
        Session::put('lifetime', $nowTime + env('LIFE_SEC', 10800));

        return $next($request);
    }
}
