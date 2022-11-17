<?php

namespace App\Http\Middleware;

use App\Http\Controllers\API\BaseController as BaseController;
use Closure;
use Illuminate\Http\Request;

class AppAuthKeyMiddleware extends BaseController
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $app_auth_key = $request->header('App-Auth-Key');

        if (env('APP_AUTH_KEY', 'default-app-auth-key') == $app_auth_key)
            return $next($request);

        return $this->sendError('Invalid app auth key!', [], 400);
    }
}
