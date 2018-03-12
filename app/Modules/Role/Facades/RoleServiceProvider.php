<?php 
namespace App\Modules\Role\Facades;

use App\Modules\Role\RoleRepository;
use Illuminate\Support\ServiceProvider;

class RoleServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('role', function($app) {
            return new RoleFacades($app->make(RoleRepository::class));
        });        
    }
}