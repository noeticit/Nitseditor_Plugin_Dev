<?php

namespace Nitseditor\System\Providers;

use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;

class PluginRouteServiceProvider extends RouteServiceProvider
{
    protected $packages = [];

    protected $namespace;

    protected $app;

    private $path;

    public function __construct(Application $app, $packages)
    {
        $this->packages = $packages;
        $this->path = base_path();
        $this->directoryPath = '/plugins/';
        parent::__construct($app);
    }


    public function boot()
    {
        parent::boot();
    }


    public function map()
    {
        $this->mapApiRoutes();

//        $this->mapWebRoutes();
    }


    protected function mapApiRoutes()
    {
        foreach ($this->packages as $package){

            $packageName = Arr::get($package, 'name');
            $namespace = 'Noetic\Plugins\\'. $packageName .'\Controllers';

            Route::prefix($packageName .'/api')
                ->middleware('api')
                ->namespace($namespace)
                ->group($this->path . $this->directoryPath . $packageName . '/Routes/api.php');
        }

    }

    protected function mapWebRoutes()
    {
        foreach ($this->packages as $package) {

            $packageName = Arr::get($package, 'name');
            $namespace = 'Noetic\Plugins\\' . $packageName . '\Controllers';

            Route::middleware('web')
                ->namespace($namespace)
                ->group($this->path . $this->directoryPath . $packageName . '/Routes/web.php');
        }
    }
}