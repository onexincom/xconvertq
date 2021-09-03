<?php

namespace App\Console\Command\XconvertQ;

use Illuminate\Support\Arr;
use App\Console\Command\BaseCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Models\DiscuzQ\Category;
use App\Models\DiscuzX\ForumForum;

class CategoryCommand extends BaseCommand
{

    protected function configure()
    {
        $this->setName($this->prefix . 'category');
        $this->setDescription('转换板块信息');
        $this->setHelp("这个命令将转换版块类型为type 和sub 的版块");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $category_status = Category::checkCategory();
        $breakpoint_continuation = config('breakpoint_continuation');
        if ($category_status && !$breakpoint_continuation) {
            $output->writeln('Q分类表中有出默认分类外的数据，请先删除再执行命令');
            return self::SUCCESS;
        }

        $output->writeln('分类转换开始:');
        $forum_query = ForumForum::convertForum();
        if ($breakpoint_continuation) {
            $max_id = (int) Category::max('id');
            $forum_query->where('fid', '>', $max_id);
        }
        $count = $forum_query->count();
        $progress = new ProgressBar($output, $count);

        $progress->setFormat('verbose');
        $progress->start();
        foreach ($forum_query->cursor() as $forum) {
            $forumfield = $forum->forumfield;
            if ($forumfield) {
                $forumfield = $forumfield->toArray();
            }
            $data = [
                'id' => Arr::get($forum, 'fid'),
                'name' => Arr::get($forum, 'name'),
                'description' => Arr::get($forumfield, 'description'),
                'icon' => Arr::get($forumfield, 'icon'),
                'sort' => (int)Arr::get($forum, 'displayorder'),
                'moderators' => '',
                'property' => 0,
                'thread_count' => Arr::get($forum, 'threads')
            ];
            Category::createCategory($data);
            $progress->advance();
        }
        $progress->finish();
        $output->writeln(' 分类转换完成');
        $output->writeln('');
        return self::SUCCESS;
    }
}