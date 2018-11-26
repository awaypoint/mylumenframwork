<?php

/**
 *
 * 自定义Application 实现自定义LOG目录
 * lumen log配置在 .env  env('APP_LOG_PATH')
 */

namespace App;

use Laravel\Lumen\Application as LumenApplication;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Application extends LumenApplication
{

    /**
     * Create a new Lumen application instance.
     *
     * @param  string|null $basePath
     * @return void
     */
    public function __construct($basePath = null)
    {
        parent::__construct($basePath);
    }

    /**
     * Get the Monolog handler for the application.
     *
     * @return \Monolog\Handler\AbstractHandler
     */
    protected function getMonologHandler()
    {
        $logPath = env('APP_LOG_PATH') ? env('APP_LOG_PATH') : storage_path('logs/');
        $logPath .= 'lumen-' . date("Ymd") . '.log';
        return (new StreamHandler(
            $logPath,
            Logger::DEBUG))->setFormatter(new LineFormatter(null, null, true, true));
    }
}