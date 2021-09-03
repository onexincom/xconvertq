<?php

namespace App\Models\DiscuzQ;

class GroupPermission extends DiscuzqBaseModel
{
    public $timestamps = false;

    protected $table = "group_permission";

    public static function createGroupPermission($data)
    {
        $permission             = new static;
        $permission->attributes = $data;
        $permission->save();
        return $permission;
    }

    /**
     * @param Category $category
     * 创建权限组
     */
    public static function createCategoryPermissions(Category $category) {
        $psermissions = self::getCategoryPermissions($category);
        foreach ($psermissions as $key => $value) {
            if (!self::query()->where('group_id', $value['group_id'])->where('permission', $value['permission'])->exists()) {
                self::createGroupPermission($value);
            }
        }
    }

    /**
     * @param Category $category
     * 删除权限组
     */
    public static function deleteCategoryPermissions(Category $category)
    {
        $psermissions = self::getCategoryPermissions($category);
        foreach ($psermissions as $key => $value) {
            self::query()->where('group_id', $value['group_id'])->where('permission', $value['permission'])->delete();
        }
    }

    /**
     * @param Category $category
     * @return array
     * 获取转换权限组
     */
    public static function getCategoryPermissions(Category $category)
    {
        $prefix = 'category' . $category->id;
        $psermissions = [
            [
                'group_id' => 7,
                'permission' => $prefix . '.viewThreads',
            ],
            [
                'group_id' => 10,
                'permission' => $prefix . '.createThread',
            ],
            [
                'group_id' => 10,
                'permission' => $prefix . '.replyThread',
            ],
            [
                'group_id' => 10,
                'permission' => $prefix . '.viewThreads',
            ]
        ];
        return $psermissions;
    }
}