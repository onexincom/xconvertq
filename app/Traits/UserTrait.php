<?php

namespace App\Traits;

trait UserTrait
{
    /**
     * 获取discuzx 用户头像目录
     * @param $uid
     * @param string $size
     * @param string $type
     * @return string
     */
    public function discuzxAvatarPath($uid, $size = 'big', $type = '')
    {
        $size = in_array($size, array('big', 'middle', 'small')) ? $size : 'middle';
        $uid = abs(intval($uid));
        $uid = sprintf("%09d", $uid);
        $dir1 = substr($uid, 0, 3);
        $dir2 = substr($uid, 3, 2);
        $dir3 = substr($uid, 5, 2);
        $typeadd = $type == 'real' ? '_real' : '';
        return $dir1.'/'.$dir2.'/'.$dir3.'/'.substr($uid, -2).$typeadd."_avatar_$size.jpg";
    }
}