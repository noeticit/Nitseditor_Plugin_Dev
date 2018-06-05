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
    protected $name = 'nitsPlugin:createTable';

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
    protected $signature = 'nitsPlugin:createTable {migrationName} {tableName} ';

    /**
     * Retrieving base path into variable
     *
     * @var string
     */
    private $basePath;

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->basePath = base_path();
        $this->directoryPath = $this->basePath . '/plugins/';
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $migrationName = $this->argument('migrationName');
        $tableName = $this->argument('tableName');
        $plugins = $this->getPlugins();
        $time = Carbon::now();
        if(count($plugins) > 1)
        {
            $this->info('You have multiple plugins installed');
            $pluginName = $this->ask('Enter the plugin name');
            $path = $this->directoryPath . $pluginName .'/nitseditor.php';
            if(!File::exists($path))
            {
                $this->info('Plugin does not exists');
            }
            else
            {
                $dbPath = $this->directoryPath .'/'. $pluginName . '/Databases/Migrations' . $time->format('Y_m_d_His'). '_create_'. $migrationName . '_table.php';
                File::put($dbPath, $this->makeDatabaseContent($migrationName , $tableName, $pluginName ));
            }
        }
        else
        {
            foreach($plugins as $plugin)
            {
                $dbPath = $plugin . '/Databases/Migrations' . $time->format('Y_m_d_His'). '_create_'. $migrationName . '_table.php';
                $pluginName = str_replace($this->directoryPath, '', $plugin);
                File::put($dbPath, $this->makeDatabaseContent($migrationName , $tableName, $pluginName));
            }
        }

    }

    public function getPlugins()
    {
        $list = File::directories($this->directoryPath);
        return $list;
    }

    public function makeDatabaseContent($migrationName , $tableName, $pluginName)
    {
        $tableNameArr = explode('=',$tableName);
        return '<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Create'. ucfirst($migrationName) .'Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public static function up()
    {
        Schema::create(\''. $tableNameArr[1] .'\', function (Blueprint $table) {
            $table->increments("id");

            $table->timestamps();
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public static function down()
    {
        Schema::dropIfExists(\''. $tableNameArr[1] .'\');
    }
}
    ';
    }
}