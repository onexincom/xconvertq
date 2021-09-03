<?php

namespace App\Models\DiscuzQ;

class Category extends DiscuzqBaseModel
{

    public static function checkCategory()
    {
        return static::query()->where('id' , '>', 1)->exists();
    }

    public static function createCategory(array $data) {
        $category = new static();
        $category->attributes = $data;
        if ($category->save()) {
            GroupPermission::createCategoryPermissions($category);
        }
        return $category;
    }
}