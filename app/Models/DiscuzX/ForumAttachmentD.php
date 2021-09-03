<?php

namespace App\Models\DiscuzX;

class ForumAttachmentD extends DiscuzxBaseModel
{
    protected $table = "forum_attachment_3";

    /**
     * 重定义主键
     *
     * @var string
     */
    protected $primaryKey = 'aid';

}