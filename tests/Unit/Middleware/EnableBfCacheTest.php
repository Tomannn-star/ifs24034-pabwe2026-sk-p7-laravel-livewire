<?php

namespace Tests\Unit\Middleware;

use App\Http\Middleware\EnableBfCache;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class EnableBfCacheTest extends TestCase
{
    public function test_removes_no_store_from_cache_control_on_get_request(): void
    {
        $middleware = new EnableBfCache();
        $request = Request::create('/stocks', 'GET');

        $response = $middleware->handle($request, function () {
            $res = new Response('content', 200);
            $res->headers->set('Cache-Control', 'no-cache, must-revalidate, no-store, max-age=0, private');
            return $res;
        });

        $cacheControl = (string) $response->headers->get('Cache-Control');
        $this->assertStringNotContainsString('no-store', $cacheControl);
        $this->assertStringContainsString('no-cache', $cacheControl);
    }

    public function test_leaves_cache_control_untouched_if_no_store_not_present(): void
    {
        $middleware = new EnableBfCache();
        $request = Request::create('/stocks', 'GET');

        $response = $middleware->handle($request, function () {
            $res = new Response('content', 200);
            $res->headers->set('Cache-Control', 'public, max-age=3600');
            return $res;
        });

        $this->assertEquals('max-age=3600, public', (string) $response->headers->get('Cache-Control'));
    }

    public function test_leaves_post_request_untouched(): void
    {
        $middleware = new EnableBfCache();
        $request = Request::create('/stocks', 'POST');

        $response = $middleware->handle($request, function () {
            $res = new Response('created', 201);
            $res->headers->set('Cache-Control', 'no-store, private');
            return $res;
        });

        $this->assertStringContainsString('no-store', (string) $response->headers->get('Cache-Control'));
    }
}
