<?php

namespace App\Models\DiscuzQ;

class User extends DiscuzqBaseModel
{

    public static function checkUsers()
    {
        return static::query()->where('id' , '>', 1)->exists();
    }

    public static function createUser(array $data) {
        $user = new static();
        $user->attributes = $data;
        if ($user->save()) {
            UserWallet::createUserWallet($user->id);
            GroupUser::createGroupUser($user->id);
        }
        return $user;
    }
}