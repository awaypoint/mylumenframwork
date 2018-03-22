<?php 
namespace App\Modules\Waste\Facades;

use App\Modules\Waste\WasteRepository;
use Illuminate\Support\ServiceProvider;

class WasteServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('waste', function($app) {
            return new WasteFacades($app->make(wasteRepository::class));
        });        
    }
}