<?php

declare(strict_types = 1);

namespace Amondar\Postman\Route;

use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;

/**
 * Class RouteFilter
 *
 * @author Amondar-SO
 */
final class RouteFilter
{
    use Conditionable;

    /**
     * @var array<int, Closure(Route):bool>
     */
    private array $filters = [];

    public static function apply(): RouteFilter
    {
        return new self;
    }

    public function byName(string|array $name): RouteFilter
    {
        $this->filters[] = static fn(Route $route): bool => Str::is($name, $route->name);

        return $this;
    }

    public function byPath(string|array $path): RouteFilter
    {
        $this->filters[] = static fn(Route $route): bool => Str::is($path, $route->path);

        return $this;
    }

    public function byMethod(string|array $method): RouteFilter
    {
        $method = is_array($method) ? $method : [$method];
        $this->filters[] = static fn(Route $route) => array_any($route->methods, static fn($routeMethod) => Str::contains($routeMethod, $method, true));

        return $this;
    }

    public function byMiddleware(array|string $middleware): RouteFilter
    {
        $middleware = is_array($middleware) ? $middleware : [$middleware];
        $this->filters[] = static fn(Route $route) => array_any($route->middleware, static fn($item) => Str::is($middleware, $item));

        return $this;
    }

    public function test(Route $route): bool
    {
        foreach ($this->filters as $filter) {
            if ( ! $filter($route)) {
                return false;
            }
        }

        return true;
    }
}
