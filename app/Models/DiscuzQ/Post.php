<?php

namespace App\Models\DiscuzQ;

class Post extends DiscuzqBaseModel
{


    public static function checkPost()
    {
        return static::query()->where('is_first', '<>', 1)->count();
    }

    public static function createPost(array $data) {
        $post = new static();
        $post->attributes = $data;
        $post->save();
        return $post;
    }
}