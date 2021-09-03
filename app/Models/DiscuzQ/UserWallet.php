<?php

namespace App\Models\DiscuzQ;

class UserWallet extends DiscuzqBaseModel
{
    /**
     * 创建用户钱包
     * @param  int $user_id 用户ID
     * @return \App\Models\UserWallet
     */
    public static function createUserWallet($user_id)
    {
        $user_wallet                   = new static;
        $user_wallet->user_id          = $user_id;
        $user_wallet->available_amount = 0.00;
        $user_wallet->freeze_amount    = 0.00;
        $user_wallet->wallet_status    = 0;
        $user_wallet->save();
        return $user_wallet;
    }
}