<?php

namespace App\Models\DiscuzX;

class ForumAttachmentH extends DiscuzxBaseModel
{
    protected $table = "forum_attachment_7";

    /**
     * 重定义主键
     *
     * @var string
     */
    protected $primaryKey = 'aid';

}