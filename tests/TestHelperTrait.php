<?php

namespace Tests;

use App\Models\Url;
use Faker\Factory as Faker;

trait TestHelperTrait
{
    public function fillUrlSizesTo($size, $numEntries = 10)
    {
        if ($numEntries > 1000) {
            $numEntries = 1000;
        }
        if ($numEntries > $size) {
            $numEntries = $size;
        }
        $sizes = [];

        $remainder = $size % $numEntries;

        if ($size > 0) {
            $chopSize = ($size - $remainder) / $numEntries;
            $sizes = array_merge($sizes,
                array_fill(0, $numEntries, $chopSize)
            );
        }

        if (array_key_exists(0, $sizes)) {
            $sizes[0] += $remainder;
        } else {
            $size[0] = $remainder;
        }
    }
}
