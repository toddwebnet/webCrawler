<?php

namespace App\Helpers;


class Utils
{
    public static function randomChars($length)
    {
        $chars = str_split('01234567890abcdefghijklmnopqrstuvwxyz01234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890');
        $randomChars = '';
        for($x=0;$x<$length;$x++){
            $randomChars.=$chars[rand(0, count($chars)-1)];
        }
        return $randomChars;
    }
}
