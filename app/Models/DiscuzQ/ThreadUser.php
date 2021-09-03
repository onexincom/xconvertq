<?php

namespace App\Models\DiscuzQ;

class ThreadUser extends DiscuzqBaseModel
{

    protected $table = "thread_user";

    public $timestamps = false;

    public static function createThreadUser(array $data) {
        $post_user = new static();
        $post_user->attributes = $data;
        $post_user->save();
        return $post_user;
    }
}