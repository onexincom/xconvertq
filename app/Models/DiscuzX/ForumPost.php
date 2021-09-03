<?php

namespace App\Models\DiscuzX;

use Illuminate\Support\Arr;

class ForumPost extends DiscuzxBaseModel
{
    public $table = "forum_post";

    /**
     * 重定义主键
     *
     * @var string
     */
    protected $primaryKey = 'pid';


    public static function approvedValue($invisible)
    {
        if ($invisible == 0) {
            //正常
            $isApproved = 1;
        } elseif ($invisible == -3 || $invisible == -2) {
            //忽略
            $isApproved = 2;
        } elseif ($invisible == -5 ||$invisible == -1) {
            //回收站
            $isApproved = 'delete';
        } else{
            //忽略
            $isApproved = 2;
        }
        return $isApproved;
    }

    public static function threadFirstPost(ForumThread $thread)
    {
        $table = self::getRealtable($thread);
        $post = new static;
        $post->setTable($table);
        return $post->where('first', 1)->where('tid', $thread->tid)->first();
    }

    public static function getPostsQuery(ForumThread $thread)
    {
        $table = self::getRealtable($thread);
        $post = new static;
        $post->setTable($table);
        return $post->where('first', 0)->where('tid', $thread->tid);
    }

    public static function getRealtable(ForumThread $thread)
    {
        $table_id = Arr::get($thread, 'posttableid', '');
        if (!empty($table_id)) {
            return 'forum_post_' . $table_id;
        } else {
            return 'forum_post';
        }
    }

    public function author()
    {
        return $this->hasOne(CommomMember::class, 'uid', 'authorid');
    }
}