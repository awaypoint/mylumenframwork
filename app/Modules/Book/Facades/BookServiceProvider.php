<?php 
namespace App\Modules\Book\Facades;

use App\Modules\Book\BookRepository;
use Illuminate\Support\ServiceProvider;

class BookServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('book', function($app) {
            return new BookFacades($app->make(BookRepository::class));
        });        
    }
}