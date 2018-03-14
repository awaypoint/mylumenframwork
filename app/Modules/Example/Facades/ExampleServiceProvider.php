<?php 
namespace App\Modules\_bigname_\Facades;

use App\Modules\_bigname_\_bigname_Repository;
use Illuminate\Support\ServiceProvider;

class _bigname_ServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('_smallname_', function($app) {
            return new _bigname_Facades($app->make(_smallname_Repository::class));
        });        
    }
}