<?php

namespace App\Models\DiscuzQ;

class PostUser extends DiscuzqBaseModel
{
    protected $table = "post_user";


    public $timestamps = false;

    public static function createPostUser(array $data) {
        $post_user = new static();
        $post_user->attributes = $data;
        $post_user->save();
        return $post_user;
    }
}