<?php

namespace App\Models\DiscuzX;

class ForumImageType extends DiscuzxBaseModel
{
    protected $table = "forum_imagetype";
    /**
     * 重定义主键
     *
     * @var string
     */
    protected $primaryKey = 'typeid';

    public function smiley()
    {
        return $this->hasMany(CommonSmiley::class, 'typeid');
    }


    public static function convertSmiley()
    {
        return self::query()->where('type', 'smiley');
    }
}