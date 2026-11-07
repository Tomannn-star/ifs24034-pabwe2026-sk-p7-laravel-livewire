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

        if ($request->isMethod('GET') && $response->headers->has('Cache-Control')) {
            $cacheControl = (string) $response->headers->get('Cache-Control');
            if (str_contains($cacheControl, 'no-store')) {
                $cleaned = trim(str_replace(['no-store,', ', no-store', 'no-store'], '', $cacheControl));
                $response->headers->set('Cache-Control', $cleaned);
            }
        }

        return $response;
    }
}
