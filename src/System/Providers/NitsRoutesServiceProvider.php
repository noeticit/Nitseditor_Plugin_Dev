<?php

namespace Nitseditor\System\Providers;


use App\Providers\RouteServiceProvider;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;

class NitsRoutesServiceProvider extends RouteServiceProvider
{
    protected $namespace='Nitseditor\System\Controllers';

    protected $coreNamespace = 'App\Http\Controllers';

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
            ->namespace($this->namespace)
            ->group(__DIR__ . '/../Routes/api.php');
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
        if(!$packages)
        {
            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(__DIR__ . '/../Routes/web.php');
        }
    }
}