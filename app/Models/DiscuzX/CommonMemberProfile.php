<?php

namespace App\Models\DiscuzX;

class CommonMemberProfile extends DiscuzxBaseModel
{
    protected $table = "common_member_profile";

    /**
     * 重定义主键
     *
     * @var string
     */
    protected $primaryKey = 'uid';

}