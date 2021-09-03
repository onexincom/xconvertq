<?php

namespace App\Console\Command\UtilsCommand;

use App\Models\DiscuzX\ForumImageType;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Console\Command\BaseCommand;
use App\Models\DiscuzQ\User;
use App\Models\DiscuzQ\UserWallet;
use App\Models\DiscuzQ\Category;
use App\Models\DiscuzQ\Thread;
use App\Models\DiscuzQ\Post;
use App\Models\DiscuzQ\Attachment;
use App\Models\DiscuzQ\GroupPermission;
use App\Models\DiscuzQ\Emoji;

class CleanDatabaseCommand extends BaseCommand
{

    protected function configure()
    {
        $this->setName($this->prefix . 'cleanDatabase');
        $this->setDescription('清理转换数据表');
        $this->setHelp("清理转换数据表");

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $option = $input->getArgument('option');
        $text = '';
        $style = new SymfonyStyle($input, $output);
        if (empty($option)) {
            $option = 'all';
            $text = '请确认要清理所有转换相关的数据？将清理用户表、用户钱包表、分类表、主题表、回复表、附件表、表情转换数据';
        } else {
             switch ($option) {
                 case 'user':
                     $text = '请确认要清理Q用户数据';
                     break;
                 case 'userWallet':
                     $text = '请确认要清理Q用户钱包数据';
                     break;
                 case 'category':
                     $text = '请确认要清理Q分类数据';
                     break;
                 case 'thread':
                     $text = '请确认要清理Q主题数据';
                     break;
                 case 'post':
                     $text = '请确认要清理Q回复数据';
                     break;
                 case 'attachment':
                     $text = '请确认要清理Q附件数据';
                     break;
                 case 'emoji':
                     $text = '请确认要清理Q表情转换数据';
                     break;
                 default:
                     $style->writeln('没有对应操作');
                     return self::SUCCESS;
                     break;
             }
        }


        if (!$style->confirm($text, false)) {
            $style->writeln('已取消清理');
            return self::SUCCESS;
        }

        if ($option == 'all' || $option == 'user') {
            $result = User::where('id', '>', '1')->delete();
            if ($result) {
                $style->writeln('delete user succuss');
            }
        }

        if ($option == 'all' || $option == 'userWallet') {
            $result = UserWallet::where('user_id', '>', '1')->delete();
            if ($result) {
                $style->writeln('delete user_wallet succuss');
            }
        }


        if ($option == 'all' || $option == 'category') {
            $result = false;
            Category::where('id', '>', '1')->each(function($category) use (&$result){
                GroupPermission::deleteCategoryPermissions($category);
                $result = $category->delete();
            });
            if ($result) {
                $style->writeln('delete category succuss');
            }
        }

        if ($option == 'all' || $option == 'thread') {
            Post::where('is_first', 1)->delete();
            $result = Thread::where('id', '>', '0')->delete();
            if ($result) {
                $style->writeln('delete thread succuss');
            }
        }

        if ($option == 'all' || $option == 'post') {
            $result = Post::where('id', '>', '0')->where('is_first', 0)->delete();
            if ($result) {
                $style->writeln('delete post succuss');
            }
        }

        if ($option == 'all' || $option == 'attachment') {
            $result = Attachment::where('id', '>', '0')->delete();
            if ($result) {
                $style->writeln('delete attachment succuss');
            }
        }

        if ($option == 'all' || $option == 'emoji') {
            foreach (ForumImageType::query()->cursor() as $type) {
                Emoji::where('category', $type->directory)->delete();
            }
            $style->writeln('delete emoji succuss');
        }

        return self::SUCCESS;
    }
}