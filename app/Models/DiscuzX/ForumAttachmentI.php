<?php

namespace App\Models\DiscuzX;

class ForumAttachmentI extends DiscuzxBaseModel
{
    protected $table = "forum_attachment_8";

    /**
     * 重定义主键
     *
     * @var string
     */
    protected $primaryKey = 'aid';

}