<?php

namespace App\Http\Middleware;

use App\Models\AuthToken;
use App\Services\Responses\ApiResponse;
use App\Services\TokenService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ApiAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (env('DISABLE_API_AUTH', 0) == 0) {
            $token = $request->header('token');
            if ($token === null) {
                return (new ApiResponse(Response::HTTP_UNAUTHORIZED, 'unauthorized'))
                    ->getResponse('unauthorized');
            } else {
                if (!app()->make(TokenService::class)->isValidToken($token)) {
                    return (new ApiResponse(Response::HTTP_UNAUTHORIZED, 'unauthorized'))
                        ->getResponse('Invalid Token');
                } else {
                    session([
                        'user_id' => (AuthToken::where('token', $token)->firstOrFail())->user_id
                    ]);
                }
            }
        }
        return $next($request);
    }
}
