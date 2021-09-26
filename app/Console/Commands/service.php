<?php

namespace App\Console\Commands;

use Error;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class service extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new service';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $fileName = $this->argument('name');
        $fileName = str_replace("/", "\\", $fileName);
        $path = explode("\\", $fileName);
        $className = end($path);

        $dir = "";
        if(strpos($fileName, "\\") !== false)
        {
            $dir = "\\";
            foreach($path as $key => $item)
            {
                if($key === count($path) - 1) break;
                $dir .= $key ? "\\".$item : $item;
            }
        }
            
        try
        {
            $fileTemplate = "<?php\n\nnamespace App\\Services".$dir.";\n\nclass ".$className."\n{\n\t// Declare construct function\n\tpublic function __construct()\n\t{\n\n\t}\n\n\t// Your code here\n}";

            Storage::disk('app')->put($fileName.".php", $fileTemplate);
            echo "\e[32mCreated service successfully!\e[39m";
        }
        catch(\Throwable $e)
        {
            echo "\e[31mFailed to create service!\e[39m\n";
            echo "ERROR CODE: ".$e->getCode()."\n";
            echo "ERROR MESSAGE: ".$e->getMessage();
        }
        return true;
    }
}
