<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Console\Commands\CreateModules;

class CreateModulesServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('command.make.modules', function () {
            return new CreateModules;
        });

        $this->commands('command.make.modules');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['command.make.modules'];
    }
}
