<?php

namespace App\Models\DiscuzX;

class ForumAttachmentJ extends DiscuzxBaseModel
{
    protected $table = "forum_attachment_9";

    /**
     * 重定义主键
     *
     * @var string
     */
    protected $primaryKey = 'aid';

}