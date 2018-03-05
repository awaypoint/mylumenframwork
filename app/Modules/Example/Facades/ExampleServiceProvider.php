<?php 
namespace App\Modules\Example\Facades;

use App\Modules\Example\ExampleRepository;
use Illuminate\Support\ServiceProvider;

class ExampleServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('example', function($app) {
            return new ExampleFacades($app->make(ExampleRepository::class));
        });        
    }
}