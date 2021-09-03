<?php

namespace App\Console\Command\XconvertQ;

use App\Models\DiscuzQ\User;
use App\Models\DiscuzX\ForumThread;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use App\Console\Command\BaseCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Models\DiscuzQ\Post;
use App\Models\DiscuzQ\Thread;
use App\Models\DiscuzX\ForumPost;
use App\Traits\PostTrait;

class PostCommand extends BaseCommand
{
    use PostTrait;

    protected function configure()
    {
        $this->setName($this->prefix . 'post');
        $this->setDescription('转换回复信息');
        $this->setHelp("这个命令将转换转回复");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $post_data_status = Post::checkPost();
        $breakpoint_continuation = config('breakpoint_continuation');
        if ($post_data_status && !$breakpoint_continuation) {
            $output->writeln('Q帖子表中有数据，请先删除再执行命令');
            return self::SUCCESS;
        }

        $output->writeln('主题回复转换开始:');
        //所有转换的主题
        $thread_query = ForumThread::convertThread();
        $count = $thread_query->count();
        $progress = new ProgressBar($output, $count);

        $progress->setFormat('verbose');
        $progress->start();
        $output->writeln('');
        $post_count = 0;
        $post_bar = new ProgressBar($output, $post_count);
        $post_bar->setFormat(' %message%');
        $post_bar->setMessage('');

        $post_bar->start();

        foreach ($thread_query->cursor() as $thread) {
            if (!User::find($thread->authorid)){
                continue;
            }
            $output->write("\033[1A");
            $post_query = ForumPost::getPostsQuery($thread)->orderBy('pid', 'asc');
            if ($breakpoint_continuation) {
                $max_id = (int) Post::where('thread_id', $thread->tid)->max('id');
                $post_query->where('pid', '>', $max_id);
            }
            $post_bar_count = $post_query->count();
            $post_bar->setMaxSteps($post_bar_count);
            $post_bar->setMessage('当前主题 id ：' . $thread->tid . '，共计转换回复数：' . $post_count);

            $progress->advance();
            $output->writeln('');

            $comment_post = 1;
            //主题下的所有回帖
            foreach ($post_query->cursor() as $post) {
                if (!User::find($post->authorid)){
                    continue;
                }
                $invisible = Arr::get($post, 'invisible');
                $post_status = ForumPost::approvedValue($invisible);
                $dateline = Carbon::parse(Arr::get($thread, 'dateline'))->format('Y-m-d H:i:s');
                $post_data = [
                    'id' => Arr::get($post, 'pid'),
                    'user_id' => Arr::get($post, 'authorid'),
                    'thread_id' => Arr::get($post, 'tid'),
                    'is_first' => Arr::get($post, 'first'),
                    'created_at' => $dateline,
                    'updated_at' => Carbon::parse(Arr::get($thread, 'lastpost'))->format('Y-m-d H:i:s')
                ];

                if (empty($post_data['user_id'])) {
                    continue;//匿名贴不转
                }
                if ($post_status == 'delete') {
                    $post_data['deleted_at'] = $dateline;
                    $post_data['is_approved'] = 0;
                } else {
                    $post_data['is_approved'] = $post_status;
                }

                $result = $this->findReply(Arr::get($post, 'message'));

                $replyInfo = Arr::get($result, 'reply_info');
                if (!empty($replyInfo)) {
                    //回复用户
                    $replay_user = User::where('username', Arr::get($replyInfo, 'username'))->first();
                    if ($replay_user) {
                        $reply_pid = Arr::get($replyInfo, 'pid');
                        $reply_post =  Post::find($reply_pid);
                        if ($reply_post) {
                            if ($reply_post->is_comment) {
                                $post_data['reply_post_id'] = $reply_post->reply_post_id;
                            } else {
                                $post_data['reply_post_id'] = $reply_pid;
                            }
                            $post_data['reply_user_id'] = $replay_user->id;
                            $post_data['is_comment'] = 1;
                        }
                    }
                } else {
                    if (empty($post_data['deleted_at'])) {
                        $comment_post++;
                    }
                }

                $message = Arr::get($result, 'message');
                //$this->setConfig();
                $post_data['content'] = $this->convertMessage($message);

                Post::createPost($post_data);
                $post_count++;
                $post_bar->advance();
            }
            $discuzq_thread = Thread::find($thread->tid);
            if ($discuzq_thread) {
                $discuzq_thread->post_count = $comment_post;
                $discuzq_thread->save();
            }
        }
        $progress->finish();
        $output->writeln('');
        $output->writeln(' 主题回复转换完成' . $post_count);
        $output->writeln('');
        return self::SUCCESS;
    }
}