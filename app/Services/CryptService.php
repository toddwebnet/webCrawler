<?php

namespace App\Services;

class CryptService
{

    public function encrypt($word)
    {
        return md5($word);
    }
}
