<?php 
namespace App\Modules\Setting\Facades;

use App\Modules\Setting\SettingRepository;
use Illuminate\Support\ServiceProvider;

class SettingServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('setting', function($app) {
            return new SettingFacades($app->make(SettingRepository::class));
        });        
    }
}