<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AuthSeed extends Command
{
    protected $signature = 'auth:seed';

    public function handle()
    {
        if (\App\Models\User::where('username', 'jtodd')->count() == 0) {
            \App\Models\User::create([
                'username' => 'jtodd',
                'password' => app()->make(CryptService::class)->encrypt('password')
            ]);
        }
    }
}
