<?php

namespace Nitseditor\System\Providers;

use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

class PluginRouteServiceProvider extends RouteServiceProvider
{
//    protected $packages = [];

    protected $namespace;

    protected $coreNamespace = 'App\Http\Controllers';

    protected $nitsditorNamespace='Nitseditor\System\Controllers';

    protected $app;

    private $path;

    public function __construct(Application $app)
    {
//        $this->packages = $packages;
        $this->path = base_path();
//        $this->directoryPath = '/plugins/';
        parent::__construct($app);
    }


    public function boot()
    {
        parent::boot();
    }


    public function map()
    {
        $this->mapApiRoutes();

        $this->mapCoreRoutes();
    }


    protected function mapCoreRoutes()
    {
        //Core API routes loader
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->coreNamespace)
            ->group(base_path('routes/api.php'));

        //Core Web routes loader
        Route::middleware('web')
            ->namespace($this->coreNamespace)
            ->group(base_path('routes/web.php'));

    }

    protected function mapApiRoutes()
    {
        //Nitseditor route loader
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->nitsditorNamespace)
            ->group(__DIR__ . '/../Routes/api.php');

        //Plugin route loader
        foreach (nits_plugins() as $package){
            $namespace = nits_get_plugin_config($package.'.namespace');
            if($namespace)
            {

                if(File::exists(base_path('/plugins/'). $namespace .'/Routes/api.php', $namespace))
                {

                    Route::prefix( $namespace .'/api')
                        ->middleware('api')
                        ->namespace('Noetic\Plugins\\'. $namespace .'\Controllers')
                        ->group(base_path('plugins/') . $namespace . '/Routes/api.php');

                    Route::middleware('web')
                        ->namespace('Noetic\Plugins\\'. $namespace .'\Controllers')
                        ->group(base_path('plugins/') . $namespace . '/Routes/web.php');
                }
            }
        }
    }

    protected function mapWebRoutes()
    {
//        Nitseditor route loader
//        $config = config('nitseditor');
//        $packages = Arr::get($config, 'packages', []);
//        if(!$packages) {
//            Route::middleware('web')
//                ->namespace($this->nitsditorNamespace)
//                ->group(__DIR__ . '/../Routes/web.php');
//        }

//        Plugin route loader
//        if(config('nitseditor.old_config'))
//        {
//            foreach ($this->packages as $package) {
//
//                $packageName = Arr::get($package, 'name');
//                $namespace = 'Noetic\Plugins\\' . $packageName . '\Controllers';
//
//                Route::middleware('web')
//                    ->namespace($namespace)
//                    ->group($this->path . $this->directoryPath . $packageName . '/Routes/web.php');
//            }
//        }
    }
}
