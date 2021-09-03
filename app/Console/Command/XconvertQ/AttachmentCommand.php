<?php

namespace App\Console\Command\XconvertQ;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Console\Command\BaseCommand;
use Illuminate\Support\Carbon;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Models\DiscuzQ\Attachment;
use App\Models\DiscuzX\ForumAttachment;

class AttachmentCommand extends BaseCommand
{

    protected function configure()
    {
        $this->setName($this->prefix . 'attachment');
        $this->setDescription('转换附件');
        $this->setHelp("这个命令将转换附件");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $attach_status = Attachment::checkAttachment();

        $breakpoint_continuation = config('breakpoint_continuation');
        if ($attach_status && !$breakpoint_continuation) {
            $output->writeln('Q附件表中有数据，请先删除再执行命令');
            return self::SUCCESS;
        }

        $output->writeln('附件转换开始:');
        $attachment_query = ForumAttachment::convertAttachment();
        if ($breakpoint_continuation) {
            $max_id = (int) Attachment::max('id');
            $attachment_query->where('aid', '>', $max_id);
        }
        $count = $attachment_query->count();
        $progress = new ProgressBar($output, $count);

        $progress->setFormat('verbose');
        $progress->start();
        foreach ($attachment_query->cursor() as $attach) {
            $attachment_info = $attach->realAttachment;
            if (!empty($attachment_info)) {
                $file = Arr::get($attachment_info, 'attachment');
                $dateline = Arr::get($attachment_info, 'dateline');
                $dateline = Carbon::parse($dateline)->format('Y-m-d H:i:s');
                $data = [
                    'id' => Arr::get($attachment_info, 'aid'),
                    'uuid' =>   (string) Str::uuid(),
                    'user_id' => Arr::get($attachment_info, 'uid'),
                    'type_id' => Arr::get($attachment_info, 'pid'),
                    'order' => 0,
                    'type' => $attachment_info->isimage ? 1 : 0,
                    'is_remote' => Arr::get($attachment_info, 'remote'),
                    'is_approved' => 1,
                    'attachment' =>  basename($file),
                    'file_path' => $attachment_info->remote ? $file : 'public/attachments/' . dirname($file) . '',
                    'file_name' => Arr::get($attachment_info, 'filename'),
                    'file_size' => Arr::get($attachment_info, 'filesize'),
                    'file_type' => '',
                    'ip' => '',
                    'created_at' => $dateline,
                    'updated_at' => $dateline
                ];
                Attachment::createAttachment($data);
            }
            $progress->advance();
        }
        $progress->finish();
        $output->writeln(' 附件转换完成');
        $output->writeln('');
        return self::SUCCESS;
    }
}