<?php

namespace App\Models;

use App\Services\CryptService;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_id',
        'username',
        'password',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

}
