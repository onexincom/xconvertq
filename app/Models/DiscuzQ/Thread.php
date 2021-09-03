<?php

namespace App\Models\DiscuzQ;

class Thread extends DiscuzqBaseModel
{

    const TYPE_OF_TEXT = 0;

    const TYPE_OF_LONG = 1;//长文贴

    const TYPE_OF_VIDEO = 2;

    const TYPE_OF_IMAGE = 3;

    const UNAPPROVED = 0;

    const APPROVED = 1;

    const IGNORED = 2;

    public static function checkThread()
    {
        return static::query()->count();
    }

    public static function createThread(array $data) {
        $thread = new static();
        $thread->attributes = $data;
        $thread->save();
        return $thread;
    }
}