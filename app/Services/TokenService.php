<?php

namespace App\Services;

use App\Models\AuthToken;
use App\Models\User;

class TokenService
{

    public function __construct()
    {
        $this->garbageCleanup();
    }

    private function garbageCleanup()
    {

        AuthToken::where('expires', '<', strtotime('now - 100 days'))->delete();

    }

    public function getNewToken(User $user)
    {

        $token = AuthToken::create([
            'user_id' => $user->id,
            'token' => $this->generateToken(),
            'expires' => strtotime('now + 10 minutes')
        ]);
        return [
            'token' => $token->token
        ];
    }

    public function isValidToken($token)
    {
        return AuthToken::where('token', $token)->where('expires', '>=', time())->count() > 0;
    }

    private function generateToken()
    {
        $chars = str_split('0123456789abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');
        $numChars = 32;
        $token = '';
        for ($x = 0; $x < $numChars; $x++) {
            $token .= $chars[rand(0, count($chars) - 1)];
        }
        return $token;

    }
}
