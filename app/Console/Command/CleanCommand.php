<?php

namespace App\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Console\Command\UtilsCommand\CleanDatabaseCommand;

class CleanCommand extends BaseCommand
{
    /**
     * @var array
     * 数据清理命令
     */
    public $commands = [
        'cleanDatabase' => CleanDatabaseCommand::class,
    ];

    public function __construct()
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName($this->prefix . 'clean');
        $this->addArgument('name', InputArgument::OPTIONAL, '你想要执行什么操作?');
        $this->addArgument('option', InputArgument::OPTIONAL, '你想要执行什么操作?');
        $this->setDescription('清理转换目标表中的多余数据');
        $this->setHelp("这个命令将清理用户表、用户钱包、主题表、分类表、回复表中的数据");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $result = $this->addCommand($name);
        if (!$result) {
            $output->writeln('命令不存在:' . $name);
            return self::SUCCESS;
        }
        $this->executeCommand($input, $output);
        $output->writeln("All is done");
        return self::SUCCESS;
    }
}