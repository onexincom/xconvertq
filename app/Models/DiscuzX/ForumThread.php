<?php

namespace App\Models\DiscuzX;

class ForumThread extends DiscuzxBaseModel
{
    protected $table = "forum_thread";

    /**
     * 重定义主键
     *
     * @var string
     */
    protected $primaryKey = 'tid';

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     * 转换的主题的筛选条件，只转换普通贴
     */
    public static function convertThread()
    {
        $forum_ids = ForumForum::convertForum()->pluck('fid');

        return self::query()->where('special', 0)->whereIN('displayorder', [0, -1, -2, -3])->whereIN('fid', $forum_ids)->orderBy('tid', 'asc');
    }

    public static function approvedStatus($displayorder)
    {
        $is_approved = 0;
        if ($displayorder == 0) {
            //正常
            $is_approved = 1;
        } elseif ($displayorder == -1) {
            //回收站
            $is_approved = 'delete';
        } elseif ($displayorder == -2) {
            //待审核
            $is_approved = 2;
        } elseif ($displayorder == -3) {
            //忽略
            $is_approved = 2;
        }
        return $is_approved;
    }

    public function firstPost($table_id = 0)
    {
        return $this->hasOne(ForumPost::class, 'tid')->where('first', 1);
    }

    public function author()
    {
        return $this->hasOne(CommomMember::class, 'uid', 'authorid');
    }

    public function replyPost()
    {
        return $this->hasMany(ForumPost::class, 'tid')->where('first', 0);
    }
}