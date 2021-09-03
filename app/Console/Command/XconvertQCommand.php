<?php

namespace App\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Console\Command\XconvertQ\UserCommand;
use App\Console\Command\XconvertQ\CategoryCommand;
use App\Console\Command\XconvertQ\ThreadCommand;
use App\Console\Command\XconvertQ\PostCommand;
use App\Console\Command\XconvertQ\SettingCommand;
use App\Console\Command\XconvertQ\AttachmentCommand;
use App\Console\Command\XconvertQ\EmojiCommand;

class XconvertQCommand extends BaseCommand
{
    /**
     * @var array
     * 转换命令
     */
    public $commands = [
        'user' => UserCommand::class,
        'category' => CategoryCommand::class,
        'attachment' => AttachmentCommand::class,
        'emoji' => EmojiCommand::class,
        'thread' => ThreadCommand::class,
        'post' => PostCommand::class,
        'count' => SettingCommand::class
    ];

    public function __construct()
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName($this->prefix . 'xtq');
        $this->addArgument('name', InputArgument::OPTIONAL, '你想要执行什么操作?');
        $this->setDescription('命令执行总入口');
        $this->setHelp("选择你想执行的命令，完成转换");
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