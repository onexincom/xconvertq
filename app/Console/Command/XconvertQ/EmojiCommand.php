<?php

namespace App\Console\Command\XconvertQ;

use App\Console\Command\BaseCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Models\DiscuzQ\Emoji;
use App\Models\DiscuzX\ForumImageType;

class EmojiCommand extends BaseCommand
{

    protected function configure()
    {
        $this->setName($this->prefix . 'emoji');
        $this->setDescription('转换表情');
        $this->setHelp("这个命令将转换表情");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('表情转换开始:');
        $emoji_query = ForumImageType::convertSmiley();
        $count = $emoji_query->count();

        if ($count <= 0) {
            $output->writeln(' 没有表情文件');
            return self::SUCCESS;
        }
        $progress = new ProgressBar($output, $count);

        $progress->setFormat('verbose');
        $progress->start();
        $output->writeln('');


        $emoji_bar = new ProgressBar($output, $count);
        $emoji_bar->setFormat(' %message%');
        $emoji_bar->setMessage('');
        $emoji_bar->start();

        foreach ($emoji_query->cursor() as $emoji_type) {
            $output->write("\033[1A");
            $emoji_count = $emoji_type->smiley()->count();
            $emoji_bar->setMaxSteps($emoji_count);
            $emoji_bar->setMessage('当前表情 id ：' . $emoji_type->tid . '，共计转换表情数：' . $emoji_count);

            $progress->advance();
            $output->writeln('');
            foreach ($emoji_type->smiley()->cursor() as $emoji) {
                $code =  preg_replace(['/\{/', '/\}/'], ['[', ']'], $emoji->code);

                $q_emoji = Emoji::where('code', $code)->where('category',$emoji_type->directory)->first();

                if (empty($q_emoji)) {
                    $data = [
                        'category' => $emoji_type->directory,
                        'code' => $code,
                        'order' => 0,
                        'url'  => 'emoji/' . $emoji_type->directory . '/' . $emoji->url,
                    ];
                    Emoji::createEmoji($data);
                } else {
                    $q_emoji->order = 0;
                    $q_emoji->code = $code;
                    $q_emoji->url = 'emoji/' . $emoji_type->directory . '/' . $emoji->url;
                    $q_emoji->save();
                }
                $emoji_bar->advance();
            }
        }

        $progress->finish();
        $output->writeln(' 表情转换完成');
        $output->writeln('');
        return self::SUCCESS;
    }
}