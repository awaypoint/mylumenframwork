<?php

namespace App\Http\Middleware;

use Closure;

class RequestLogMiddleware
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
        $dirname = env('REQUEST_LOG_DIR', '/Users/away/Documents/another/requestLog/');
        $data = [
            'method' => $request->getMethod(),
            'client_ip' => $request->getClientIp(),
            'params' => $request->all(),
            'uri' => $request->getPathInfo(),
        ];
        doLog($dirname, json_encode($data));
        return $next($request);
    }
}
