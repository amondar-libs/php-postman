<?php

declare(strict_types = 1);

namespace Tests;

use Amondar\Postman\Laravel\LaravelRoutesParser;
use Amondar\Postman\Laravel\PostmanRouteRegistrarServiceProvider;
use Amondar\Postman\Laravel\PostmanRouterServiceProvider;
use Amondar\Postman\Laravel\PostmanRouteServiceProvider;
use Amondar\Postman\Route\RouteCollection;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Tests\_fixtures\Routes;

abstract class WithLaravelTestCase extends BaseTestCase
{
    protected RouteCollection $routes;

    protected function setUp(): void
    {
        parent::setUp();

        $this->routes = (new LaravelRoutesParser)->parseLaravelRoutes(
            $this->app->make('router')->getRoutes()
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            PostmanRouteServiceProvider::class,
            PostmanRouteRegistrarServiceProvider::class,
            PostmanRouterServiceProvider::class,
        ];
    }

    /**
     * Define routes setup.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    protected function defineRoutes($router)
    {
        Routes::define($router);
    }
}
