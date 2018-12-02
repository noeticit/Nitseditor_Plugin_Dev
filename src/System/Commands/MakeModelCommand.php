<?php

namespace Nitseditor\System\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeModelCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'nits:model';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for creation of Nitseditor Plugin\'s Model.';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nits:model {modelName}';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $modelName = $this->argument('modelName');
        if(count($this->getPlugins()) > 1)
        {
            $this->info('You have multiple plugins installed');
            $pluginName = $this->ask('Enter the plugin name');
            $path = base_path('plugins') . $pluginName .'/nitseditor.php';
            !File::exists($path) ? $this->info('Plugin does not exists') : $this->makeModelContent($modelName, $pluginName);
        }
        else
        {
            foreach($this->getPlugins() as $plugin)
            {
                $pluginName = str_replace(base_path('plugins'), '', $plugin);
                $this->makeModelContent($modelName, $pluginName);
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
    protected function makeModelContent($name, $pluginName)
    {
        $modelTemplate = str_replace(
            ['{{modelName}}', '{{pluginName}}'],
            [ucfirst(strtolower($name)), $pluginName],
            $this->getStub('Model')
        );

        file_put_contents(base_path("plugins/{$pluginName}/Models/{$name}.php"), $modelTemplate);
    }

}