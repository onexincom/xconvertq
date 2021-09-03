<?php

namespace App\Models\DiscuzX;

class ForumAttachmentF extends DiscuzxBaseModel
{
    protected $table = "forum_attachment_5";

    /**
     * 重定义主键
     *
     * @var string
     */
    protected $primaryKey = 'aid';

}