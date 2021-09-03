<?php

namespace App\Models\DiscuzX;

class ForumAttachmentC extends DiscuzxBaseModel
{
    protected $table = "forum_attachment_2";

    /**
     * 重定义主键
     *
     * @var string
     */
    protected $primaryKey = 'aid';

}