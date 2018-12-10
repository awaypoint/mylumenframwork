<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception $e
     * @return void
     */
    public function report(Exception $e)
    {
        //parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        $httpOrigin = is_null($request->server('HTTP_ORIGIN')) ? '*' : $request->server('HTTP_ORIGIN');
        header('Access-Control-Allow-Origin:' . $httpOrigin);
        header('Access-Control-Allow-Credentials:true');
        header('WithCredentials:true');
        if ($e instanceof HttpResponseException) {
            return $e->getResponse();
        } elseif ($e instanceof ModelNotFoundException) {
            $e = new HttpException(403, $e->getMessage(), $e, [], 404);
        } elseif ($e instanceof ValidationException && $e->getResponse()) {
            $_content['params_error'] = $e->validator->getMessageBag()->all();
            $e = new HttpException(200, $e->validator->getMessageBag()->first(), $e, [], 400);
        } elseif ($e instanceof NotFoundHttpException) {
            $e = new HttpException(403, $e->getMessage(), $e, [], 404);
        } elseif ($e instanceof MethodNotAllowedHttpException) {
            $e = new HttpException(200, '亲～ 服务器暂不提供该Api', $e, [], 404);
        } elseif ($e instanceof QueryException) {
            $e = new HttpException(200, 'DB查询错误', $e, [], 4000001);
        } else {
            $e = new HttpException(200, $e->getMessage(), $e, [], $e->getCode() > 0 ? $e->getCode() : 500);
        }
        $_content['msg'] = $e->getMessage();
        $_content['code'] = $e->getCode();

        $_isDebug = env('APP_DEBUG', false);
        if ($_isDebug) {
            $_trace['line'] = $e->getLine();
            $_trace['type'] = get_class($e);
            $_trace['file'] = $e->getFile();
            $_trace['sql'] = DB::connection('mysql')->getQuerylog();
            if ($e->getPrevious()) {
                $_trace['file'] = $e->getPrevious()->getFile();
                $_trace['file_line'] = $e->getPrevious()->getLine();
            }
            $_content['trace'] = $_trace;
        }

        $response = new Response($_content, 200);
        $response->exception = $e;

        $response->send();
        exit();
    }
}
