<?php

namespace App\Console\Command\XconvertQ;

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use App\Console\Command\BaseCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Models\DiscuzQ\Thread;
use App\Models\DiscuzQ\Post;
use App\Models\DiscuzQ\User;
use App\Models\DiscuzX\ForumThread;
use App\Models\DiscuzX\ForumPost;
use App\Traits\PostTrait;

class ThreadCommand extends BaseCommand
{
    use PostTrait;

    protected function configure()
    {
        $this->setName($this->prefix . 'thread');
        $this->setDescription('转换主题信息');
        $this->setHelp("这个命令将转换主题");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        // ini_set("memory_limit","300M");
        $thread_status = Thread::checkThread();
        $breakpoint_continuation = config('breakpoint_continuation');
        if ($thread_status && !$breakpoint_continuation) {
            $output->writeln('Q主题表中有数据，请先删除再执行命令');
            return self::SUCCESS;
        }

        $output->writeln('主题转换开始:');


        $thread_query = ForumThread::convertThread();
        if ($breakpoint_continuation) {
            $max_id = (int) Thread::max('id');
            $thread_query->where('tid', '>', $max_id);
        }
        $count = $thread_query->count();

        $progress = new ProgressBar($output, $count);

        $progress->setFormat('verbose');
        $progress->start();

        foreach ($thread_query->cursor() as $thread) {

            if (empty($thread->authorid)){
                //匿名贴不转
                continue;
            }
            if (!User::find($thread->authorid)){
                continue;
            }
            $first_post = ForumPost::threadFirstPost($thread);
            if ($first_post) {
                $first_post = $first_post->toArray();
            } else {
                //first_post不存在
                continue;
            }

            if (empty($first_post['authorid'])){
                //匿名贴不转
                continue;
            }
            $thread_data = [
                'id' => Arr::get($thread, 'tid'),
                'user_id' => Arr::get($thread, 'authorid'),
                'category_id' => Arr::get($thread, 'fid'),
                'type' => Thread::TYPE_OF_LONG,
                'title' => Arr::get($thread, 'subject'),
                'post_count' =>  intval(Arr::get($thread, 'replies')) + 1,
                'view_count' => Arr::get($thread, 'views'),
                'address' => '',
                'location' => '',
                'longitude' => 0,
                'latitude' => 0,
                'created_at' => Carbon::parse(Arr::get($thread, 'dateline'))->format('Y-m-d H:i:s')
            ];
            $displayorder = Arr::get($thread, 'displayorder');
            $thread_status = ForumThread::approvedStatus($displayorder);
            if ($thread_status == 'delete') {
                $thread_data['deleted_at'] = Carbon::parse(Arr::get($thread, 'dateline'))->format('Y-m-d H:i:s');
                $thread_data['is_approved'] = 0;
            } else {
                $thread_data['is_approved'] = $thread_status;
            }


            $invisible = Arr::get($first_post, 'invisible');

            $post_status = ForumPost::approvedValue($invisible);
            $dateline = Carbon::parse(Arr::get($thread, 'dateline'))->format('Y-m-d H:i:s');
            $post_data = [
                'id' => Arr::get($first_post, 'pid'),
                'user_id' => Arr::get($first_post, 'authorid'),
                'thread_id' => Arr::get($first_post, 'tid'),
                'is_first' => Arr::get($first_post, 'first'),
                'created_at' => $dateline,
                'updated_at' => Carbon::parse(Arr::get($thread, 'lastpost'))->format('Y-m-d H:i:s')
            ];

            if ($post_status == 'delete') {
                $post_data['deleted_at'] = $dateline;
                $post_data['is_approved'] = 0;
            } else {
                $post_data['is_approved'] = $post_status;
            }

            $message = Arr::get($first_post, 'message');

            $this->setConfig();
            $post_data['content'] = $this->convertMessage($message);

            Thread::createThread($thread_data);
            Post::createPost($post_data);

            $progress->advance();
        }
        $progress->finish();
        $output->writeln(' 主题转换完成');
        $output->writeln('');
        return self::SUCCESS;
    }
}