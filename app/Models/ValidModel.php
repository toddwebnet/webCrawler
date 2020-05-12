<?php

namespace App\Models;

use App\Models\Scopes\ValidScope;
use Illuminate\Database\Eloquent\Model;

class ValidModel extends Model
{
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new ValidScope());
    }
}
