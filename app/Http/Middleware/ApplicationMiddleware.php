<?php

namespace App\Http\Middleware;

use App\Services\TokenCrud;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApplicationMiddleware
{
    private TokenCrud $tokenCrud;

    public function __construct(TokenCrud $tokenCrud)
    {
        $this->tokenCrud = $tokenCrud;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Todo, mandar a un service
        $headerToken = $request->header('Application-Token');
        if (empty($headerToken)) {
            return response()->json(['message' => 'Token not provided'], 401);
        }
        try {
            $token = $this->tokenCrud->findByToken($headerToken);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        if ($token->isExpired()) {
            return response()->json(['message' => 'Token expired'], 401);
        }
        return $next($request);
    }
}
