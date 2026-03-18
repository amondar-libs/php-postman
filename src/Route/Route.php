<?php

declare(strict_types = 1);

namespace Amondar\Postman\Route;

use Amondar\Postman\Auth\None;
use Amondar\Postman\Blueprints\Request;
use Amondar\Postman\Blueprints\RequestData;
use Amondar\Postman\Contracts\AuthenticationContract;
use Amondar\Postman\Enums\Method;
use Generator;
use Illuminate\Support\Collection;
use Stringable;

/**
 * Class Route
 *
 * @immutable
 *
 * @author Amondar-SO
 */
final readonly class Route
{
    /**
     * Route constructor.
     */
    public function __construct(
        public ?string $name,
        public string $path,
        public array $methods,
        public ?RouteAction $action,
        public ?string $domain = null,
        public array $middleware = [],
        public ?string $alias = null,
        public string|Stringable|null $description = null,
        public ?int $structureDepth = null,
        public Collection $additionalHeaders = new Collection,
        public ?AuthenticationContract $auth = null
    ) {
        //
    }

    /**
     * Assigns authentication details to the route.
     *
     * @param  AuthenticationContract  $auth  The authentication mechanism to be associated with the route.
     */
    public function withAuth(AuthenticationContract $auth): Route
    {
        return new self(
            name: $this->name,
            path: $this->path,
            methods: $this->methods,
            action: $this->action,
            domain: $this->domain,
            middleware: $this->middleware,
            alias: $this->alias,
            description: $this->description,
            structureDepth: $this->structureDepth,
            auth: $auth
        );
    }

    /**
     * Return a structured name for tree building.
     */
    public function getStructuredName(): array
    {
        if ( ! $this->name) {
            $routeNames = explode('/', $this->path);
        } else {
            $routeNames = explode('.', $this->name);

            if ($this->structureDepth !== null) {
                $routeNames = array_slice($routeNames, 0, $this->structureDepth);
            } else {
                array_pop($routeNames);
            }
        }

        return array_filter($routeNames, fn($value) => ! empty($value));
    }

    /**
     * Maps the current configuration to a generator of request objects.
     *
     * @param  string  $host  The host to be used in generating the request.
     * @return Generator<Request>
     */
    public function mapToRequestBlueprint(string $host, array $formData = []): Generator
    {
        foreach ($this->methods as $method) {
            $method = Method::fromString($method);

            if ($method->allowed()) {
                yield new Request(
                    name: $this->alias ?? $this->name ?? $this->path,
                    data: new RequestData(
                        path: $this->path,
                        host: $host,
                        method: $method,
                        headers: $this->additionalHeaders,
                        auth: $this->auth ?? new None,
                        description: $this->description,
                        formData: $formData
                    )
                );
            }
        }
    }
}
