<?php

namespace Tests\Unit\Middleware;

use App\Http\Middleware\EnableBfCache;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class EnableBfCacheTest extends TestCase
{
    public function test_removes_no_store_directive_on_get_request(): void
    {
        $middleware = new EnableBfCache();
        $request = Request::create('/stocks', 'GET');

        $response = $middleware->handle($request, function () {
            $res = new Response('content', 200);
            $res->headers->addCacheControlDirective('no-store', true);
            $res->headers->set('Pragma', 'no-cache');
            $res->headers->set('Expires', 'Fri, 01 Jan 1990 00:00:00 GMT');
            return $res;
        });

        $this->assertFalse($response->headers->hasCacheControlDirective('no-store'));
        $this->assertFalse($response->headers->has('Pragma'));
        $this->assertFalse($response->headers->has('Expires'));
    }

    public function test_leaves_post_request_untouched(): void
    {
        $middleware = new EnableBfCache();
        $request = Request::create('/stocks', 'POST');

        $response = $middleware->handle($request, function () {
            $res = new Response('created', 201);
            $res->headers->addCacheControlDirective('no-store', true);
            return $res;
        });

        $this->assertTrue($response->headers->hasCacheControlDirective('no-store'));
    }
}
