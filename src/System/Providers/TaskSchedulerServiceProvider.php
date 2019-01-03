<?php
namespace Nitseditor\System\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

class TaskSchedulerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule->command('some:command')->everyMinute();
        });
    }

    public function register()
    {
        //
    }
}