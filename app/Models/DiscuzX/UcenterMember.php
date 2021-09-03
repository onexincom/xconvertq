<?php

namespace App\Models\DiscuzX;

use App\Models\UserWechat;

class UcenterMember extends DiscuzxBaseModel
{
    protected $table = "ucenter_members";

    /**
     * 重定义主键
     *
     * @var string
     */
    protected $primaryKey = 'uid';
}