<?php

namespace App\Models\DiscuzQ;

class Emoji extends DiscuzqBaseModel
{
    public static function createEmoji(array $data) {
        $attachment = new static();
        $attachment->attributes = $data;
        $attachment->save();
        return $attachment;
    }
}