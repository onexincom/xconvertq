<?php

namespace App\Models\DiscuzX;

class ForumAttachmentG extends DiscuzxBaseModel
{
    protected $table = "forum_attachment_6";

    /**
     * 重定义主键
     *
     * @var string
     */
    protected $primaryKey = 'aid';

}