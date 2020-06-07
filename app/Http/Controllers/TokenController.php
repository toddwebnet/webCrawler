<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Responses\ApiError;
use App\Services\Responses\ApiResponse;
use App\Services\TokenService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TokenController
{

    public function get(Request $request)
    {

        $userService = app()->make(UserService::class);
        $username = $request->input('username');
        $password = $request->input('password');

        if (!$userService->authenticate(
            $username,
            $password
        )) {
            return (new ApiResponse(Response::HTTP_UNAUTHORIZED))
                ->getResponse('unauthorized');
        }

        try {
            $tokenObject = app()->make(TokenService::class)->getNewToken(
                User::where('username', $username)->firstOrFail()
            );
        } catch (\Exception $e) {
            return new ApiError($e->getMessage());
        }

        return $tokenObject;

    }

    public function get2(Request $request)
    {
        /** @var UserService $userService */
        $userService = app()->make(UserService::class);

        if ($userService->authenticate(
            $request->input('username'),
            $request->input('password')
        )) {
            return app()->make(TokenService::class)->getNewToken($request->input('username'));
            return (new ApiResponse(Response::HTTP_OK))
                ->getResponse('pass');
        } else {
            return (new ApiResponse(Response::HTTP_UNAUTHORIZED))
                ->getResponse('unauthorized');
        }
    }
}
