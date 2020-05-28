<?php

namespace App\Services;

use App\Helpers\Utils;

class PathNameService
{
    public function getRandomPathName()
    {
        return sha1(
            Utils::randomChars(6) .
            time() .
            Utils::randomChars(6)
        );
    }

}
