<?php

declare(strict_types = 1);

namespace Amondar\Postman\Laravel;

use Amondar\Postman\Contracts\AuthenticationContract;
use Illuminate\Routing\Route;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Stringable;

/**
 * Class PostmanRouteServiceProvider
 *
 * @author Amondar-SO
 */
class PostmanRouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::macro('alias', function (string $alias): Route {
            /** @var Route $this */
            $this->action[ 'postmanAlias' ] = $alias;
            $this->action[ 'postman' ] = true;

            return $this;
        });

        Route::macro('auth', function (AuthenticationContract $type): Route {
            /** @var Route $this */
            $this->action[ 'postmanAuth' ] = $type;
            $this->action[ 'postman' ] = true;

            return $this;
        });

        Route::macro('description', function (Stringable|string $docs): Route {
            // Instantiate simple Stringable objects
            if (is_string($docs) && class_exists($docs)) {
                $docs = new $docs;
            }

            if ($docs instanceof Stringable) {
                $docs = $docs->__toString();
            }

            /** @var Route $this */
            $this->action[ 'postmanDescription' ] = $docs;
            $this->action[ 'postman' ] = true;

            return $this;
        });

        Route::macro('additionalHeaders', function (array $headers): Route {
            /** @var Route $this */
            if ($headers !== []) {
                $this->action[ 'postmanHeaders' ] = $headers;
                $this->action[ 'postman' ] = true;
            }

            return $this;
        });

        Route::macro('structureDepth', function (int $depth): Route {
            /** @var Route $this */
            $this->action[ 'postmanDepth' ] = $depth;
            $this->action[ 'postman' ] = true;

            return $this;
        });

        Route::macro('getPostmanAction', function (?string $key = null): mixed {
            /** @var Route $this */
            $data = $this->getAction('postmanExtracted');

            if ($data === null) {
                $data = $this->action['postmanExtracted'] = (new Collection($this->getAction()))
                    ->filter(fn(mixed $value, string $key) => Str::startsWith($key, 'postman'))
                    ->mapWithKeys(fn(mixed $value, string $key) => [
                        ($key === 'postman' ? $key : Str::camel(Str::replace('postman', '', $key))) => $value,
                    ])->all();
            }

            return $key ? ($data[$key] ?? null) : $data;
        });
    }
}
