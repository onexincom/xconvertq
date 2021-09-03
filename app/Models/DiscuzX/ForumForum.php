<?php

namespace App\Models\DiscuzX;

class ForumForum extends DiscuzxBaseModel
{
    protected $table = "forum_forum";

    /**
     * 重定义主键
     *
     * @var string
     */
    protected $primaryKey = 'fid';


    public function forumfield()
    {
        return $this->hasOne(ForumForumfield::class, 'fid');
    }

    public static function convertForum()
    {
        return self::query()->where('status', 1)->where('fid', '>', 1)->where(function ($query) {
            $query->where('type', 'forum')
                ->orWhere('type', 'sub');
        });
    }
}