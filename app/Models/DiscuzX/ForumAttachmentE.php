<?php

namespace App\Models\DiscuzX;

class ForumAttachmentE extends DiscuzxBaseModel
{
    protected $table = "forum_attachment_4";

    /**
     * 重定义主键
     *
     * @var string
     */
    protected $primaryKey = 'aid';

}