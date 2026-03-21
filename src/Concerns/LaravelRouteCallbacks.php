<?php

declare(strict_types = 1);

namespace Amondar\Postman\Concerns;

use Amondar\Postman\Blueprints\Header;
use Amondar\Postman\Contracts\AuthenticationContract;
use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Stringable;

/**
 * Trait RouteCallbacks
 *
 * @author Amondar-SO
 */
trait LaravelRouteCallbacks
{
    /**
     * Return callback for ->aliasedName('Some name') fns.
     */
    protected static function aliasCallback(string $attribute = 'action'): Closure
    {
        return function (string $alias) use ($attribute) {
            /** @var \Illuminate\Routing\Route|\Illuminate\Routing\RouteRegistrar $self */
            /** @phpstan-ignore-next-line */
            $self = $this;
            $self->{$attribute}[ 'postmanAlias' ] = $alias;
            $self->{$attribute}[ 'postman' ] = true;

            return $self;
        };
    }

    /**
     * Return callback for ->description(Markdown::line('Some paragraph text.')) fns.
     */
    protected static function descriptionCallback(string $attribute = 'action'): Closure
    {
        return function (Stringable|string $docs) use ($attribute) {
            /** @var \Illuminate\Routing\Route|\Illuminate\Routing\RouteRegistrar $self */
            /** @phpstan-ignore-next-line */
            $self = $this;

            // Instantiate simple Stringable objects
            if (is_string($docs) && class_exists($docs)) {
                $docs = new $docs;
            }

            if ($docs instanceof Stringable) {
                $docs = $docs->__toString();
            }

            $self->{$attribute}[ 'postmanDescription' ] = $docs;
            $self->{$attribute}[ 'postman' ] = true;

            return $self;
        };
    }

    /**
     * Return callback for ->structureDepth(3) fns
     */
    protected static function structureDepthCallback(string $attribute = 'action'): Closure
    {
        return function (int $depth) use ($attribute) {
            /** @var \Illuminate\Routing\Route|\Illuminate\Routing\RouteRegistrar $self */
            /** @phpstan-ignore-next-line */
            $self = $this;
            $self->{$attribute}[ 'postmanDepth' ] = $depth;
            $self->{$attribute}[ 'postman' ] = true;

            return $self;
        };
    }

    /**
     * Return callback for ->authType() fns.
     */
    protected static function authCallback(string $attribute = 'action'): Closure
    {
        return function (AuthenticationContract $type) use ($attribute) {
            /** @var \Illuminate\Routing\Route|\Illuminate\Routing\RouteRegistrar $self */
            /** @phpstan-ignore-next-line */
            $self = $this;
            $self->{$attribute}[ 'postmanAuth' ] = $type;
            $self->{$attribute}[ 'postman' ] = true;

            return $self;
        };
    }

    /**
     * Return callback for processing additional headers.
     */
    protected static function additionalHeadersCallback(string $attribute = 'action'): Closure
    {
        return function (array $headers) use ($attribute) {
            /** @var \Illuminate\Routing\Route|\Illuminate\Routing\RouteRegistrar $self */
            /** @phpstan-ignore-next-line */
            $self = $this;
            $headersBag = (new Collection($headers))
                ->filter(
                    fn($item) => is_array($item)
                                 && isset($item[ 'key' ]) && is_string($item[ 'key' ])
                                 && isset($item[ 'value' ]) && (is_string($item[ 'value' ]) || is_numeric($item[ 'value' ]))
                                 && isset($item[ 'type' ]) && is_string($item[ 'type' ])
                )
                ->values()
                ->map(fn($item) => new Header($item['key'], $item['value'], $item['type']));

            if ($headersBag->isNotEmpty()) {
                $self->{$attribute}[ 'postmanHeaders' ] = $headersBag;
                $self->{$attribute}[ 'postman' ] = true;
            }

            return $self;
        };
    }

    /**
     * Return callback for ->getPostmanAction() fns.
     */
    protected static function getPostmanActionCallback(): Closure
    {
        return function (?string $key = null) {
            /** @var \Illuminate\Routing\Route $self */
            /** @phpstan-ignore-next-line */
            $self = $this;
            $data = $self->getAction('postmanExtracted');

            if ($data === null) {
                $data = $self->action['postmanExtracted'] = (new Collection($self->getAction()))
                    ->filter(fn(mixed $value, string $key) => Str::startsWith($key, 'postman'))
                    ->mapWithKeys(fn(mixed $value, string $key) => [
                        ($key === 'postman' ? $key : Str::camel(Str::replace('postman', '', $key))) => $value,
                    ])->all();
            }

            return $key ? ($data[$key] ?? null) : $data;
        };
    }
}
