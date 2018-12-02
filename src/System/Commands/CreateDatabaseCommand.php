<?php

namespace Nitseditor\System\Commands;


use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CreateDatabaseCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'nits:table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for creation of Nitseditor Plugin\'s Table.';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nits:table {tableName} ';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $migrationName = $this->argument('tableName');
        if(count($this->getPlugins()) > 1)
        {
            $this->info('You have multiple plugins installed');
            $pluginName = $this->ask('Enter the plugin name');
            $path = base_path('plugins') . $pluginName .'/nitseditor.php';
            !File::exists($path) ? $this->info('Plugin does not exists') : $this->makeDatabaseContent($migrationName, $pluginName);
        }
        else
        {
            foreach($this->getPlugins() as $plugin)
            {
                $pluginName = str_replace(base_path('plugins'), '', $plugin);
                $this->makeDatabaseContent($migrationName , $pluginName);
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
     * @return array
     */
    public function getPlugins()
    {
        $list = File::directories(base_path('plugins'));
        return $list;
    }

    public function makeDatabaseContent($migrationName, $pluginName)
    {
        $migrationClass = ucfirst(str_plural(strtolower($migrationName)));
        $fileName = Carbon::now()->format('Y_m_d_His'). '_create_'. str_plural(strtolower($migrationName)). '_table';
        $databaseTemplate = str_replace(
            ['{{MigrationClass}}', '{{tableName}}'],
            [$migrationClass, str_plural(strtolower($migrationName))],
            $this->getStub('Database')
        );

        file_put_contents(base_path("plugins/{$pluginName}/Databases/Migrations/{$fileName}.php"), $databaseTemplate);
    }
}