<?php 
namespace App\Modules\Website\Facades;

use App\Modules\Website\WebsiteRepository;
use Illuminate\Support\ServiceProvider;

class WebsiteServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('website', function($app) {
            return new WebsiteFacades($app->make(WebsiteRepository::class));
        });        
    }
}