<?php

namespace App\Models\DiscuzX;

class ForumForumfield extends DiscuzxBaseModel
{
    protected $table = "forum_forumfield";

    /**
     * 重定义主键
     *
     * @var string
     */
    protected $primaryKey = 'fid';
}