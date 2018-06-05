<?php

namespace Nitseditor\System\Providers;

use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Nitseditor\System\Commands\CreateDatabaseCommand;
use Nitseditor\System\Commands\CreatePluginCommand;
use Nitseditor\System\Commands\CreateRequestCommand;
use Nitseditor\System\Commands\MakeControllerCommand;
use Nitseditor\System\Commands\MakeModelCommand;
use Illuminate\Support\Facades\Event;
use Laravel\Passport\Events\AccessTokenCreated;
use Nitseditor\System\Providers\ProviderRepository;
use Illuminate\Database\Eloquent\Factory;
use Faker\Generator as Faker;

class NitsEditorServiceProvider extends ServiceProvider
{
    /**
     *   Bootstrapping the application services
     *
     *  @return void
     *
     */
    public function boot(ProviderRepository $providers)
    {
        Schema::defaultStringLength(191);

        $this->app->register('Nitseditor\System\Providers\NitsRoutesServiceProvider');

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

        $config = config('nitseditor');
        $packages = Arr::get($config, 'packages', []);

        $routeDir = new PluginRouteServiceProvider($this->app, $packages);
        $this->app->register($routeDir);

        foreach ($packages as $package) {

            $packageName = Arr::get($package, 'name');

            $this->loadViewsFrom(base_path().'/plugins/'. $packageName .'/Views', $packageName);

            $this->loadMigrationsFrom(base_path().'/plugins/'. $packageName .'/Databases');

            $this->app->singleton(Factory::class, function () use($packageName){
                $faker = $this->app->make(Faker::class);
                return Factory::construct($faker,base_path().'/plugins/'. $packageName . '/Databases/Factories');
            });

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
        $migrationsPath = __DIR__.'/../Database/Migrations';
        $this->loadMigrationsFrom($migrationsPath);
    }
}