<?php

namespace App\Console\Command\XconvertQ;

use App\Console\Command\BaseCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Models\DiscuzQ\Thread;
use App\Models\DiscuzQ\Post;
use App\Models\DiscuzQ\User;
use App\Models\DiscuzQ\Setting;

class SettingCommand extends BaseCommand
{

    protected function configure()
    {
        $this->setName($this->prefix . 'count');
        $this->setDescription('更新统计信息');
        $this->setHelp("这个命令将更新帖子数、用户数、回复数");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $output->writeln('数据统计开始：');
        $progress = new ProgressBar($output, 3);

        $progress->setFormat('verbose');
        $progress->start();

        $thread_count = Thread::count();
        $setting_thread_count = Setting::where('key', 'thread_count')->first();

        if ($setting_thread_count) {
            Setting::where('key', 'thread_count')->update(['value' => $thread_count]);
        } else {
            Setting::insert(['key' => 'thread_count', 'value' => $thread_count]);
        }

        $progress->advance();

        $user_count = User::count();
        $setting_user_count = Setting::where('key', 'user_count')->first();
        if ($setting_user_count) {
            Setting::where('key', 'user_count')->update(['value' => $user_count]);
        } else {
            Setting::insert(['key' => 'user_count', 'value' => $user_count]);
        }

        $post_count = Post::count();

        $setting_post_count = Setting::where('key', 'post_count')->first();
        if ($setting_post_count) {
            Setting::where('key', 'post_count')->update(['value' => $post_count]);
        } else {
            Setting::insert(['key' => 'post_count', 'value' => $post_count]);
        }

        $progress->finish();
        $output->writeln(' 统计更新完成');
        $output->writeln('');
        return self::SUCCESS;
    }
}