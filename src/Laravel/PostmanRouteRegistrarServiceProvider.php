<?php

declare(strict_types = 1);

namespace Amondar\Postman\Laravel;

use Amondar\Postman\Contracts\AuthenticationContract;
use Illuminate\Routing\RouteRegistrar;
use Illuminate\Support\ServiceProvider;

/**
 * Class PostmanRouteRegistrarServiceProvider
 *
 * @author Amondar-SO
 */
class PostmanRouteRegistrarServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        RouteRegistrar::macro('auth', function (AuthenticationContract $type): RouteRegistrar {
            $key = 'attributes';

            /** @var RouteRegistrar $this */
            $this->{$key}[ 'postmanAuth' ] = $type;
            $this->{$key}[ 'postman' ] = true;

            return $this;
        });

        RouteRegistrar::macro('additionalHeaders', function (array $headers): RouteRegistrar {
            /** @var RouteRegistrar $this */
            if ($headers !== []) {
                $key = 'attributes';
                $this->{$key}[ 'postmanHeaders' ] = $headers;
                $this->{$key}[ 'postman' ] = true;
            }

            return $this;
        });

        RouteRegistrar::macro('structureDepth', function (int $depth): RouteRegistrar {
            $key = 'attributes';

            /** @var RouteRegistrar $this */
            $this->{$key}[ 'postmanDepth' ] = $depth;
            $this->{$key}[ 'postman' ] = true;

            return $this;
        });
    }
}
