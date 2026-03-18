<?php

declare(strict_types = 1);

namespace Amondar\Postman\Laravel;

use Amondar\Postman\Contracts\RouteParserContract;
use Amondar\Postman\Route\Route;
use Amondar\Postman\Route\RouteAction;
use Amondar\Postman\Route\RouteCollection;
use Illuminate\Routing\Route as LaravelRoute;
use Illuminate\Routing\RouteCollectionInterface;
use Illuminate\Support\Collection;

/**
 * Class LaravelRoutesParser
 *
 * @internal
 *
 * @author Amondar-SO
 */
final readonly class LaravelRoutesParser implements RouteParserContract
{
    public function parse(string $rootPath): RouteCollection
    {
        $app = $this->bootstrap($rootPath);

        /** @var \Illuminate\Routing\Router $router */
        $router = $app->make('router');

        return $this->parseLaravelRoutes($router->getRoutes());
    }

    public function parseLaravelRoutes(RouteCollectionInterface $routes): RouteCollection
    {
        $collection = new RouteCollection;

        foreach ($routes->getRoutes() as $route) {
            $collection->push(
                new Route(
                    name: $route->getName(),
                    path: $route->uri(),
                    methods: $route->methods(),
                    action: $this->getRouteAction($route),
                    domain: $route->domain(),
                    middleware: $route->middleware(),
                    alias: $route->getPostmanAction('alias'),
                    description: $route->getPostmanAction('description'),
                    structureDepth: $route->getPostmanAction('depth'),
                    additionalHeaders: $route->getPostmanAction('headers') ?? new Collection,
                    auth: $route->getPostmanAction('auth')
                )
            );
        }

        return $collection;
    }

    private function bootstrap(string $rootPath): \Illuminate\Foundation\Application
    {
        /** @var \Illuminate\Foundation\Application|null $app */
        $app = app();

        if ( ! $app?->isBooted()) {
            $laravelRoot = mb_rtrim($rootPath, DIRECTORY_SEPARATOR);

            // 1) Create Laravel app
            /** @var \Illuminate\Foundation\Application $app */
            $app = require_once $laravelRoot . '/bootstrap/app.php';

            // 2) Bootstrap Laravel "console" kernel
            /** @var \Illuminate\Contracts\Console\Kernel $kernel */
            $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);

            // This boots providers, loads routes, etc.
            $kernel->bootstrap();

        }

        return $app;
    }

    private function getRouteAction(LaravelRoute $route): RouteAction
    {
        if ($controller = $route->getControllerClass()) {
            $method = $route->getActionMethod();

            return new RouteAction(
                controller: $controller,
                method: $method === $controller ? '__invoke' : $method
            );
        }

        return new RouteAction(
            closure: $route->getAction('uses')
        );

    }
}
