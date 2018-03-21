<?php 
namespace App\Modules\Files\Facades;

use App\Modules\Files\FilesRepository;
use Illuminate\Support\ServiceProvider;

class FilesServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('files', function($app) {
            return new FilesFacades($app->make(filesRepository::class));
        });        
    }
}