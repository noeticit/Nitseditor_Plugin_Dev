<?php

namespace Nitseditor\System\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;


class CreatePluginCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'nits:plugin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for creation of Nitseditor Plugin.';

    /**
     * Create a new command instance.
     *
     */

    private $basePath;


    public function __construct()
    {
        parent::__construct();
        $this->basePath = base_path();
        $this->directoryPath = $this->basePath. '/plugins/';
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Create plugins for NitsEditor');
        $pluginName = $this->ask('Tell us your plugin name:');

        $path = $this->directoryPath . $pluginName .'/nitseditor.php';
        if(!File::exists($path))
        {
            if(!File::isDirectory($this->directoryPath))
            {
                File::makeDirectory($this->directoryPath);
            }
            $folderStatus = $this->makeFolders($pluginName);
            $fileStatus = $this->makeFiles($pluginName);

            if($fileStatus == true && $folderStatus == true)
            {
                $this->info('Your plugin is created, and default files has also being created');
            }
            else
            {
                $this->info('There is some error in making plugin, delete your plugin folder and try again');
            }

        }
        else
        {
            $this->info('Error this plugin already exists');
        }
    }

    public function makeFolders($pluginName)
    {
        File::makeDirectory($this->directoryPath . $pluginName);
        File::makeDirectory($this->directoryPath . $pluginName .'/Controllers');
        File::makeDirectory($this->directoryPath . $pluginName .'/Controllers/Middlewares');
        File::makeDirectory($this->directoryPath . $pluginName .'/Routes');
        File::makeDirectory($this->directoryPath . $pluginName .'/Views');
        File::makeDirectory($this->directoryPath . $pluginName .'/Models');
        File::makeDirectory($this->directoryPath . $pluginName .'/Databases');
        File::makeDirectory($this->directoryPath . $pluginName .'/Databases/Migrations');
        File::makeDirectory($this->directoryPath . $pluginName .'/Databases/Factories');
        File::makeDirectory($this->directoryPath . $pluginName .'/Databases/Seeds');
        File::makeDirectory($this->directoryPath . $pluginName .'/Events');
        File::makeDirectory($this->directoryPath . $pluginName .'/Exceptions');
        File::makeDirectory($this->directoryPath . $pluginName .'/Listeners');
        File::makeDirectory($this->directoryPath . $pluginName .'/Providers');
        return true;
    }

    public function makeFiles($pluginName)
    {
        File::put($this->directoryPath . $pluginName .'/Routes/web.php', $this->makeWebRoutesContent($pluginName));
        File::put($this->directoryPath . $pluginName .'/Routes/api.php', $this->makeWebRoutesContent($pluginName));
        File::put($this->directoryPath . $pluginName .'/Databases/seeds/InstallDatabase.php', $this->makeSeeders($pluginName));
        File::put($this->directoryPath . $pluginName .'/Databases/seeds/UninstallDatabase.php', $this->makeSeeders($pluginName));
        File::put($this->directoryPath . $pluginName .'/nitseditor.php', 'Hello');
        File::put($this->directoryPath . $pluginName .'/Views/home.blade.php', $this->makeViewsContent($pluginName));
        File::put($this->directoryPath . $pluginName .'/Controllers/HomeController.php', $this->makeControllerContents($pluginName));
        return true;
    }

    public function makeWebRoutesContent($pluginName)
    {
        return '<?php

Route::get(\'/\', [\'as\' => \'' . $pluginName. '\', \'uses\' => \'HomeController@index\']);';

    }

    public function makeViewsContent($pluginName)
    {
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Nitseditor</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: \'Raleway\', sans-serif;
            font-weight: 100;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .content {
            text-align: center;
        }

        .title {
            font-size: 84px;
        }

        .links > a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .m-b-md {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
<div class="flex-center position-ref full-height">

    <div class="content">
        <div class="title m-b-md">
            NitsEditor -'. $pluginName .'
        </div>

        <div class="links">
            <a href="https://www.noeticitservices.com">Company Website</a>
            <a href="https://packagist.org/packages/noeticitservices/plugindev">Packagist Readme Page</a>
            <a href="https://github.com/noeticit/Nitseditor_Plugin_Dev">GitHub Page</a>
        </div>
    </div>
</div>
</body>
</html>
';
    }

    public function makeControllerContents($pluginName)
    {
        return '<?php

namespace Noetic\Plugins\\' . $pluginName . '\Controllers;
        
              
use App\Http\Controllers\Controller;
        
class HomeController extends Controller
{
    public function index()
    {
        return view(\'' . $pluginName . '::home\');
    }
}';
    }

    public function makeSeeders($pluginName)
    {
        return '<?php
namespace Noetic\Plugins\blog\Databases\seeds;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
    }
}';
    }
}