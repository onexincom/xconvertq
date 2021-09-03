<?php

namespace App\Console\Command\XconvertQ;

use App\Console\Command\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Models\DiscuzQ\User;
use App\Models\DiscuzX\CommomMember;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use App\Traits\UserTrait;
use Symfony\Component\Console\Helper\ProgressBar;

class UserCommand extends BaseCommand
{
    use UserTrait;

    protected function configure()
    {
        $this->setName($this->prefix . 'user');
        $this->setDescription('从 discuz!X 转换用户数据到 discuz!Q');
        $this->setHelp("转换用户");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $breakpoint_continuation = config('breakpoint_continuation');
        $user_status = User::checkUsers();
        if ($user_status && !$breakpoint_continuation) {
            $output->writeln('Q用户表有除user_id = 1 之外的数据无法继续执行用户转换，请先删除再执行命令');
            return self::SUCCESS;
        }
        $output->writeln('用户转开始:');

        $user_query = CommomMember::query();
        if ($breakpoint_continuation) {
            $max_id = (int)User::max('id');
            $user_query->where('uid', '>', $max_id);
        } else {
            $user_query->where('uid', '>', 1);
        }
        $count = $user_query->count();
        $progress = new ProgressBar($output, $count);
        $progress->setFormat('verbose');
        $progress->start();


        foreach ($user_query->cursor() as $member) {
            if (empty($member->ucMember)) {
                $progress->advance();
                continue;
            }
            $uc_member = $member->ucMember->toArray();
            $member_count = $member->memberCount;
            $member_profile = $member->memberProfile;
            if ($member_count) {
                $member_count = $member_count->toArray();
            }
            $avatar = '';
            $uid = Arr::get($uc_member, 'uid');
            if ($member->avatarstatus) {
                $avatar = $this->discuzxAvatarPath($uid);
            }
            $date = Carbon::parse(Arr::get($uc_member, 'regdate'))->format('Y-m-d H:i:s');
            $regip = Arr::get($uc_member, 'regip', '');


            $data = [
                'id' => $uid,
                'username' => Arr::get($uc_member, 'username'),
                'status' => CommomMember::getStatus($member->status),
                'password' => Arr::get($uc_member, 'password'),
                'avatar' => $avatar,
                'register_ip' => checkIp($regip) ? $regip : '',
                'salt' => Arr::get($uc_member, 'salt', ''),
                'updated_at' => $date,
                'created_at' => $date,
                'thread_count' => Arr::get($member_count, 'threads', 0),
                'mobile' => Arr::get($member_profile, 'mobile', ''),
            ];


            //discuz!x 身份证未验证，不转
            //            if (Arr::get($member_profile, 'idcardtype') == '身份证') {
            //                $data['identity'] = Arr::get($member_profile, 'idcard', '');
            //                $data['realname'] = Arr::get($member_profile, 'realname', '');
            //            }
            if (!empty($data['username'])) {
                if (User::where('username', $data['username'])->first()) {
                    $data['username'] .= mt_rand(1000000, 9999999);
                }
                $user = User::createUser($data);
            }
            $progress->advance();
        }
        $progress->finish();
        $output->writeln(' 用户转换完成');
        $output->writeln('');
        return self::SUCCESS;
    }
}