<?php

namespace App\Http\Middleware;

use App\Exceptions\ErrorMessageException;
use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JWTMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @throws ErrorMessageException
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (empty($request->bearerToken())) {
            throw new ErrorMessageException('Token is not found', Response::HTTP_BAD_REQUEST);
        }
        try {
            $key = env('JWT_SECRET', 'JwT-bacbon-accss-secret-hesp-rndm-txt');
            $decoded = (array) JWT::decode($request->bearerToken(), new Key($key, env('JWT_ALGO', 'HS256')));
            $request->merge(['jwt_user' => $decoded]);

            return $next($request);
        } catch (\Throwable $th) {
            throw new ErrorMessageException($th->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }
}
