<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnableBfCache
{
    /**
     * Remove 'no-store' directive on GET requests to allow back/forward cache (bfcache) restoration.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->isMethod('GET')) {
            $response->headers->removeCacheControlDirective('no-store');
            $response->headers->remove('Pragma');
            $response->headers->remove('Expires');
        }

        return $response;
    }
}
