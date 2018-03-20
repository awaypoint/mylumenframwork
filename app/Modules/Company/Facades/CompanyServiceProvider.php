<?php 
namespace App\Modules\Company\Facades;

use App\Modules\Company\CompanyRepository;
use Illuminate\Support\ServiceProvider;

class CompanyServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('company', function($app) {
            return new CompanyFacades($app->make(companyRepository::class));
        });        
    }
}