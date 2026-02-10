<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAppKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $header = $request->header('X-App-Key')
                  ?? $request->header('x-app-key')
                  ?? $_SERVER['HTTP_X_APP_KEY']
                  ?? $_SERVER['HTTP_x_app_key']
                  ?? null;

        if ($header !== env('FRONTEND_APP_KEY')) {
            abort(403, 'Forbidden: Invalid App Key');
        }

        return $next($request);
    }
}
