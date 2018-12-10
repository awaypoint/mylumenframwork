<?php

namespace App\Http\Middleware;

use App\Logs;
use Closure;

class RequestLogMiddleware
{
    private $_logModel;

    public function __construct(
        Logs $logs
    )
    {
        $this->_logModel = $logs;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->_logModel->host = $request->getHost();
        $this->_logModel->method = $request->getMethod();
        $this->_logModel->client_ip = $request->getClientIp();
        $this->_logModel->params = json_encode($request->all());
        $this->_logModel->uri = $request->getPathInfo();
        $this->_logModel->save();
        return $next($request);
    }
}
