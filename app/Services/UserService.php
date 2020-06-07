<?php

namespace App\Services;

use App\Models\User;

class UserService
{

    public function authenticate($username, $password)
    {

        $cryptService = app()->make(CryptService::class);

        return (User::where([
                'username' => $username,
                'password' => $cryptService->encrypt($password)
            ])
                ->count() > 0);
    }
}
