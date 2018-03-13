<?php

namespace App\Http\Middleware;

use App\Modules\Role\Facades\Role;
use Closure;
use Illuminate\Support\Facades\Session;

class PermissionsMiddleware
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
        $segments = $request->segments();
        $thisPermission = implode('/', $segments);
        $permissions = Role::getUserPermissions(Session::get('uid', 0));
        if (!in_array($thisPermission, $permissions)) {
            return responseTo('您暂无该接口权限', 402);
        }
        return $next($request);
    }
}
