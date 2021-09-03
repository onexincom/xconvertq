<?php

namespace App\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;

class BaseCommand extends Command
{
    public $commands = [
    ];

    public $prefix = 'app:';

    public function __construct()
    {
        parent::__construct();
    }

    protected function addCommand($name = 'all')
    {
        if (empty($name) || $name == 'all') {
            //
        } elseif (array_key_exists($name, $this->commands)) {
            $this->commands = [
                $name => $this->commands[$name]
            ];
        } else {
            return false;
        }
        foreach ($this->commands as $key => $value) {
            $reflectionClass = new \ReflectionClass($value);
            if ($reflectionClass->IsInstantiable()) {
                $this->getApplication()->add(new $value());
            } else {
                return false;
            }
        }
        return true;
    }

    protected function executeCommand(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->commands as $key => $value) {
            $name =  $this->prefix . $key;
            $command = $this->getApplication()->find($name);
            $command->execute($input, $output);
        }
    }

}