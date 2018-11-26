<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../helpers/helpers.php';

try {
    (new Dotenv\Dotenv(__DIR__ . '/../'))->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    //
}
/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/
define('APP_PATH', realpath(__DIR__ . '/../'));
$app = new App\Application(
    APP_PATH
);

$app->configure('cors');
$app->configure('auth');
$app->configure('session');

$app->withFacades();

$app->withEloquent();

/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/

$app->middleware([
    \Illuminate\Session\Middleware\StartSession::class,
    \Barryvdh\Cors\HandleCors::class,
    \App\Http\Middleware\RequestLogMiddleware::class,
]);

$app->routeMiddleware([
    'auth' => App\Http\Middleware\Authenticate::class,
    'permissions' => App\Http\Middleware\PermissionsMiddleware::class,
]);

/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/

$app->register(App\Providers\AppServiceProvider::class);
$app->register(Barryvdh\Cors\ServiceProvider::class);
$app->register(App\Providers\AuthServiceProvider::class);
// $app->register(App\Providers\EventServiceProvider::class);
$app->register(\Illuminate\Session\SessionServiceProvider::class);
$app->register(\Illuminate\Redis\RedisServiceProvider::class);
//commond
$app->register(\App\Providers\CreateModulesServiceProvider::class);

//oauth2
$app->register(Laravel\Passport\PassportServiceProvider::class);
$app->register(Dusterio\LumenPassport\PassportServiceProvider::class);
//facades
$app->register(\App\Modules\User\Facades\UserServiceProvider::class);
$app->register(\App\Modules\Role\Facades\RoleServiceProvider::class);
$app->register(\App\Modules\Setting\Facades\SettingServiceProvider::class);
$app->register(\App\Modules\Files\Facades\FilesServiceProvider::class);
$app->register(\App\Modules\Company\Facades\CompanyServiceProvider::class);


/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/

Dusterio\LumenPassport\LumenPassport::routes($app);
$app->group([
    'namespace' => 'App\Http\Controllers',
], function ($app) {
    require __DIR__ . '/../routes/web.php';
});
$app->alias('session', 'Illuminate\Session\SessionManager');

return $app;
