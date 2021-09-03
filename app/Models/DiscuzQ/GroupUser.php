<?php

namespace App\Models\DiscuzQ;

class GroupUser extends DiscuzqBaseModel
{
    protected $table = "group_user";

    public $timestamps = false;

    public static function createGroupUser($user_id, $group_id = 10)
    {
        $group_user             = new static;
        $group_user->user_id    = $user_id;
        $group_user->group_id   = $group_id;
        $group_user->save();
        return $group_user;
    }
}