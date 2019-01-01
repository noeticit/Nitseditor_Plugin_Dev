<?php

namespace Nitseditor\System\Providers;

use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

class PluginRouteServiceProvider extends RouteServiceProvider
{
    protected $packages = [];

    protected $namespace;

    protected $coreNamespace = 'App\Http\Controllers';

    protected $nitsditorNamespace='Nitseditor\System\Controllers';

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

        $this->mapWebRoutes();
    }


    protected function mapApiRoutes()
    {
        //Core routes loader
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->coreNamespace)
            ->group(base_path('routes/api.php'));

        //Nitseditor route loader
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->nitsditorNamespace)
            ->group(__DIR__ . '/../Routes/api.php');

        //Plugin route loader
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
        //Core routes loader
        Route::middleware('web')
            ->namespace($this->coreNamespace)
            ->group(base_path('routes/web.php'));

        //Nitseditor route loader
        $config = config('nitseditor');
        $packages = Arr::get($config, 'packages', []);
        if(!$packages) {
            Route::middleware('web')
                ->namespace($this->nitsditorNamespace)
                ->group(__DIR__ . '/../Routes/web.php');
        }

        //Plugin route loader
        if(config('nitseditor.old_config'))
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
}