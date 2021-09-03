<?php

namespace App\Models\DiscuzX;

class CommomMember extends DiscuzxBaseModel
{
    protected $table = "common_member";

    /**
     * 重定义主键
     *
     * @var string
     */
    protected $primaryKey = 'uid';


    public function ucMember()
    {
        return $this->hasOne(UcenterMember::class, 'uid');
    }

    public function memberCount()
    {
        return $this->hasOne(CommonMemberCount::class, 'uid');
    }

    public function memberProfile()
    {
        return $this->hasOne(CommonMemberProfile::class, 'uid');
    }

    public static function getStatus($status) {
        switch ($status) {
            case -1:
                //禁用
                $convert_status = 1;
                break;
            case 0:
                //禁用正常
                $convert_status = 0;
                break;
            default:
                //审核中
                $convert_status = 2;
        }
        return $convert_status;
    }
}