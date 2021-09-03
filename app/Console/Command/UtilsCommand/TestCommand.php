<?php

namespace App\Console\Command\UtilsCommand;

use App\Traits\PostTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Console\Command\BaseCommand;
use Illuminate\Database\Capsule\Manager as Capsule;

class TestCommand extends BaseCommand
{
    use PostTrait;

    protected function configure()
    {
        $this->setName($this->prefix . 'test');
        $this->setDescription('检查数据库连接状态');
        $this->setHelp("检查数据库连接状态");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $status = true;

        $database = require bathPath() . '/config/database.php';
        if (is_array($database)) {
            foreach ($database as $name => $config) {
                try {
                    $discuzq_connection = Capsule::connection($name);
                    $tables = $discuzq_connection->statement('show databases');
                } catch (\Exception $e) {
                    $status = false;
                    $output->writeln($name . ' 数据库异常:' . $e->getMessage());
                    break;
                }
            }
        }
        if ($status) {
            $output->writeln('恭喜测试通过！');
        }
        return self::SUCCESS;
    }
}