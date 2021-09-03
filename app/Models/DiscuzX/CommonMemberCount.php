<?php

namespace App\Models\DiscuzX;

class CommonMemberCount extends DiscuzxBaseModel
{
    protected $table = "common_member_count";

    /**
     * 重定义主键
     *
     * @var string
     */
    protected $primaryKey = 'uid';

}