<?php 
namespace App\Modules\User\Facades;

use App\Modules\User\UserRepository;
use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('user', function($app) {
            return new UserFacades($app->make(UserRepository::class));
        });        
    }
}