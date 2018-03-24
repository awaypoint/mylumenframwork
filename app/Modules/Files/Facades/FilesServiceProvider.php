<?php 
namespace App\Modules\Files\Facades;

use App\Modules\Files\FilesRepository;
use Illuminate\Support\ServiceProvider;

class FilesServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('myfiles', function($app) {
            return new FilesFacades($app->make(FilesRepository::class));
        });        
    }
}