<?php

namespace Nitseditor\System\Commands;


use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CreateRequestCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'nits:request';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for creation of Nitseditor Plugin\'s Request.';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nits:request {requestName}';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $requestName = $this->argument('requestName');
        if(count($this->getPlugins()) > 1)
        {
            $this->info('You have multiple plugins installed');
            $pluginName = $this->ask('Enter the plugin name');
            $path = base_path('plugins') . $pluginName .'/nitseditor.php';
            !File::exists($path) ? $this->info('Plugin does not exists') : $this->makeRequestContent($requestName, $pluginName);
        }
        else
        {
            foreach($this->getPlugins() as $plugin)
            {
                $pluginName = str_replace(base_path('plugins'), '', $plugin);
                $this->makeRequestContent($requestName, $pluginName);
            }
        }

    }

    /**
     * Get the stubs
     * @param $type
     * @return bool|string
     */
    protected function getStub($type)
    {
        return file_get_contents(base_path("vendor/noeticitservices/plugindev/src/System/Stubs/$type.stub"));
    }

    /**
     * Get the plugins
     * @return array
     */
    public function getPlugins()
    {
        $list = File::directories(base_path('plugins'));
        return $list;
    }

    /**
     * @param $name
     * @param $pluginName
     */
    public function makeRequestContent($name, $pluginName)
    {
        $requestName = ucfirst(strtolower($name)).'Request';
        $requestTemplate = str_replace(
            ['{{requestName}}', '{{pluginName}}'],
            [$requestName, $pluginName],
            $this->getStub('Request')
        );

        file_put_contents(base_path("plugins/{$pluginName}/Requests/{$requestName}.php"), $requestTemplate);
    }
}