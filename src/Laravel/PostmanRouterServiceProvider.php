<?php

declare(strict_types = 1);

namespace Amondar\Postman\Laravel;

use Amondar\Postman\Contracts\AuthenticationContract;
use Illuminate\Routing\Router;
use Illuminate\Routing\RouteRegistrar;
use Illuminate\Support\ServiceProvider;

/**
 * Class PostmanRouterServiceProvider
 *
 * @author Amondar-SO
 */
class PostmanRouterServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Router::macro('auth', function (AuthenticationContract $type) {
            /** @var Router $this */
            return (new RouteRegistrar($this))->auth($type);
        });

        Router::macro('additionalHeaders', function (array $headers) {
            /** @var Router $this */
            return (new RouteRegistrar($this))->additionalHeaders($headers);
        });

        Router::macro('structureDepth', function (int $depth) {
            /** @var Router $this */
            return (new RouteRegistrar($this))->structureDepth($depth);
        });
    }
}
