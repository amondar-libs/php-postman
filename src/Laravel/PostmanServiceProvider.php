<?php

declare(strict_types = 1);

namespace Amondar\Postman\Laravel;

use Amondar\Postman\Concerns\LaravelRouteCallbacks;
use Amondar\Postman\Contracts\AuthenticationContract;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Routing\RouteRegistrar;
use Illuminate\Support\ServiceProvider;

/**
 * Class PostmanServiceProvider
 *
 * @author Amondar-SO
 */
class PostmanServiceProvider extends ServiceProvider
{
    use LaravelRouteCallbacks;

    /**
     * Registers custom route macros for enhancing route functionality.
     *
     * This method defines and registers several route macros to extend the
     * features of the routing system by introducing utilities for postman actions,
     * route aliasing, structure depth, authentication types, model documentation,
     * description setting, and additional headers.
     */
    public function boot(): void
    {
        // Extend Route class with postman-related methods
        $this->registerRouteMacros();

        // Extend RouteRegistrar class with postman-related methods
        $this->registerRouterRegistrarMacros();

        // Register macros for Router class to fallback to RouteRegistrar extended methods.
        $this->registerRouterMacros();
    }

    protected function registerRouteMacros(): void
    {
        Route::macro('alias', self::aliasCallback());
        Route::macro('auth', self::authCallback());
        Route::macro('description', self::descriptionCallback());
        Route::macro('additionalHeaders', self::additionalHeadersCallback());
        Route::macro('structureDepth', self::structureDepthCallback());
        Route::macro('getPostmanAction', self::getPostmanActionCallback());
    }

    protected function registerRouterRegistrarMacros() {
        RouteRegistrar::macro('auth', self::authCallback('attributes'));
        RouteRegistrar::macro('additionalHeaders', self::additionalHeadersCallback('attributes'));
        RouteRegistrar::macro('structureDepth', self::structureDepthCallback('attributes'));
    }

    protected function registerRouterMacros()
    {
        Router::macro('auth', function (AuthenticationContract $type) {
            /** @var Router $self */
            $self = $this;

            return (new RouteRegistrar($self))->auth($type);
        });

        Router::macro('additionalHeaders', function (array $headers) {
            /** @var Router $self */
            $self = $this;

            return (new RouteRegistrar($self))->additionalHeaders($headers);
        });

        Router::macro('structureDepth', function (int $depth) {
            /** @var Router $self */
            $self = $this;

            return (new RouteRegistrar($self))->structureDepth($depth);
        });
    }
}
