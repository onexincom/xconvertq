<?php

namespace App\Console;

use App\Console\Command\XconvertQCommand as XconvertQ;
use App\Console\Command\CleanCommand as Clean;
use App\Console\Command\UtilsCommand\TestCommand as Test;
use Illuminate\Database\Capsule\Manager as Capsule;
use Symfony\Component\Console\Application;

class Convert
{
    public $application;

    public function __construct()
    {
        $this->application = new Application();
        $this->loadDatabase();
        $this->addCommand();
    }

    public function addCommand()
    {
        $this->application->add(new XconvertQ());
        $this->application->add(new Clean());
        $this->application->add(new Test());
    }

    public function loadDatabase()
    {
        $database = require bathPath() . '/config/database.php';
        if (is_array($database)) {
            $capsule = new Capsule();
            foreach ($database as $name => $config) {
                $capsule->addConnection((array) $config, $name);
            }
            $capsule->setAsGlobal();
            $capsule->bootEloquent();
        }
    }

    public function run()
    {
        $this->application->run();
    }
}
