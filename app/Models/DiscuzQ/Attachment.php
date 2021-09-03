<?php

namespace App\Models\DiscuzQ;

class Attachment extends DiscuzqBaseModel
{
    const TYPE_OF_IMAGE = 1;

    public static function checkAttachment()
    {
        return static::query()->where('id' , '>', 1)->exists();
    }

    public static function createAttachment(array $data) {
        $attachment = new static();
        $attachment->attributes = $data;
        $attachment->save();
        return $attachment;
    }
}