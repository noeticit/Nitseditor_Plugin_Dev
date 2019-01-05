<?php

namespace Nitseditor\System\Providers;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Nitseditor\System\Commands\CreateCrudCommand;
use Nitseditor\System\Commands\CreateDatabaseCommand;
use Nitseditor\System\Commands\CreatePluginCommand;
use Nitseditor\System\Commands\CreateRequestCommand;
use Nitseditor\System\Commands\MakeControllerCommand;
use Nitseditor\System\Commands\MakeModelCommand;
use Illuminate\Support\Facades\Event;
use Laravel\Passport\Events\AccessTokenCreated;
use Illuminate\Database\Eloquent\Factory;
use Faker\Generator as Faker;

class NitsEditorServiceProvider extends ServiceProvider
{
    /**
     *   Bootstrapping the application services
     *
     * @param \Nitseditor\System\Providers\ProviderRepository $providers
     * @return void
     */
    public function boot(ProviderRepository $providers)
    {
        Schema::defaultStringLength(191);

//        $this->app->register('Nitseditor\System\Providers\NitsRoutesServiceProvider');
        $this->app->register('Nitseditor\System\Providers\TaskSchedulerServiceProvider');

        $this->publishes([
            __DIR__.'/../nitseditor.php' => config_path('nitseditor.php'),
        ]);

        $this->loadViewsFrom(__DIR__ . '/../Views', 'NitsEditor');

        $this->createAccessTokenProvider($providers);
    }

    /**
     *  Register application services
     *
     * @throws \Exception
     * @return void
     */
    public function register()
    {
        $this->registerHelpers();

        $this->registerCommands();

        if ($this->app->runningInConsole()) {
            $this->registerMigrations();
        }

        $routeDir = new PluginRouteServiceProvider($this->app);
        $this->app->register($routeDir);

        foreach (nits_plugins() as $package) {
            $namespace = nits_get_plugin_config($package.'.namespace');
            if($namespace)
            {
//                if(File::exists(base_path().'/plugins/'. $namespace .'/Views', $namespace))
//                    $this->loadViewsFrom(base_path().'/plugins/'. $namespace .'/Views', $namespace);

                if(File::exists(base_path().'/plugins/'. $namespace .'/Databases/Migrations'))
                    $this->loadMigrationsFrom(base_path().'/plugins/'. $namespace .'/Databases/Migrations');

                if(File::exists(base_path().'/plugins/'. $namespace . '/Databases/Factories'))
                {
                    $this->app->singleton(Factory::class, function () use($namespace){
                        $faker = $this->app->make(Faker::class);
                        return Factory::construct($faker,base_path().'/plugins/'. $namespace . '/Databases/Factories');
                    });
                }
            }

        }

        $this->app->singleton('nitseditor', function ($app)
        {
           return new NitsEditor;
        });
    }

    /**
     * Register Commands.
     *
     * @return void
     */
    public function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CreatePluginCommand::class,
                MakeModelCommand::class,
                MakeControllerCommand::class,
                CreateDatabaseCommand::class,
                CreateRequestCommand::class,
                CreateCrudCommand::class,
            ]);
        }
    }

    /**
     * Register helpers file
     */
    public function registerHelpers()
    {
        // Load the helpers in app/Http/helpers.php
        if (file_exists($file = __DIR__ .'/../Helpers/helpers.php')) {
            require $file;
        }
    }

    /**
     * Create access token provider when access token is created.
     *
     * @param \Nitseditor\System\Providers\ProviderRepository $providers
     * @return void
     */
    protected function createAccessTokenProvider(ProviderRepository $providers)
    {
        Event::listen(AccessTokenCreated::class, function ($event) use ($providers) {
            $provider = config('auth.guards.api.provider');
            $providers->create($event->tokenId, $provider);
        });
    }

    protected function registerMigrations()
    {
        $migrationsPath = __DIR__.'/../Database';
        $this->loadMigrationsFrom($migrationsPath);
    }
}