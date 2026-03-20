<?php

declare(strict_types = 1);

namespace Amondar\Postman;

use Amondar\ClassAttributes\Parse;
use Amondar\Postman\Attributes\PostmanFormData;
use Amondar\Postman\Auth\None;
use Amondar\Postman\Contracts\AuthenticationContract;
use Amondar\Postman\Route\RouteAction;
use Amondar\Postman\Route\RouteCollection;
use Amondar\Postman\Support\Schema;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;

/**
 * Class Export
 *
 * @author Amondar-SO
 */
readonly class Export implements Arrayable
{
    use Conditionable;

    protected string $baseUrl;

    /**
     * @var Collection<string, array<array-key, mixed>>
     */
    protected Collection $actionDataCache;

    public function __construct(
        protected RouteCollection $routes,
        string $baseUrl,
        protected AuthenticationContract $defaultAuth = new None,
        protected ?string $oauthTokenPath = null,
        protected array $dataPaths = []
    ) {
        $this->baseUrl = mb_rtrim($baseUrl, '/');

        $this->actionDataCache = new Collection;
    }

    /**
     * Creates a new instance of the class using the provided route collection and base URL.
     *
     * @param  RouteCollection  $routes  The collection of routes to use.
     * @param  string  $baseUrl  The base URL to associate with the routes.
     */
    public static function from(RouteCollection $routes, string $baseUrl): Export
    {
        return new Export(
            routes: $routes,
            baseUrl: $baseUrl
        );
    }

    /**
     * Creates a new instance of the class with the specified data paths.
     *
     * @param  mixed  ...$paths  The data paths to include in the new instance.
     */
    public function parseDataIn(...$paths): Export
    {
        return new Export(
            routes: $this->routes,
            baseUrl: $this->baseUrl,
            defaultAuth: $this->defaultAuth,
            oauthTokenPath: $this->oauthTokenPath,
            dataPaths: $paths
        );
    }

    /**
     * Configures the instance to use the provided default authentication method.
     *
     * @param  AuthenticationContract  $auth  The default authentication method to be used.
     */
    public function useDefaultAuth(AuthenticationContract $auth): Export
    {
        return new Export(
            routes: $this->routes,
            baseUrl: $this->baseUrl,
            defaultAuth: $auth,
            oauthTokenPath: $this->oauthTokenPath,
            dataPaths: $this->dataPaths
        );
    }

    /**
     * Configures the instance to use the specified OAuth token path.
     *
     * @param  string  $path  The path to be used for the OAuth token retrieval.
     */
    public function useOAuthTokenPath(string $path): Export
    {
        return new Export(
            routes: $this->routes,
            baseUrl: $this->baseUrl,
            defaultAuth: $this->defaultAuth,
            oauthTokenPath: $path,
            dataPaths: $this->dataPaths
        );
    }

    /**
     * Converts the current object to its JSON representation.
     *
     * @param  int  $options  Bitmask of JSON encode options. Defaults to 0.
     * @param  int  $depth  Maximum depth for the JSON encoding. Defaults to 512.
     *
     * @throws \Spatie\StructureDiscoverer\Exceptions\NoCacheConfigured
     */
    public function toJson(string $name = 'postman.json', ?string $description = null, int $options = 0, int $depth = 512): string
    {
        return json_encode($this->toArray($name, $description), $options, $depth);
    }

    /**
     * Converts the current object to an array representation.
     *
     * @param  string  $name  The name to use during the conversion process. Defaults to 'postman.json'.
     * @param  string|null  $description  An optional description to include in the conversion.
     *
     * @throws \Spatie\StructureDiscoverer\Exceptions\NoCacheConfigured
     */
    public function toArray(string $name = 'postman.json', ?string $description = null): array
    {
        return $this->export($name, $description)->toArray();
    }

    /**
     * Exports the schema for the provided routes and prepares it for use.
     *
     * @param  string  $name  The name of the schema file to export, defaulting to 'postman.json'.
     * @param  string|null  $description  An optional description for the schema.
     *
     * @throws \Spatie\StructureDiscoverer\Exceptions\NoCacheConfigured
     */
    protected function export(string $name = 'postman.json', ?string $description = null): Schema
    {
        $host = '{{base_url}}';
        $schema = Schema::for($name, $description)
            ->addVariable('base_url', $this->baseUrl)
            ->when(
                $this->oauthTokenPath !== null,
                fn($schema) => $schema->addVariable(
                    'oauth_full_url',
                    Str::startsWith('http', $this->oauthTokenPath) ?
                        $this->oauthTokenPath
                        : "$host/" . mb_trim($this->oauthTokenPath, '/')
                )
            );

        $formDataVocabulary = $this->dataPaths === [] ?
            collect()
            : Parse::attribute(PostmanFormData::class)->ascend()->in(...$this->dataPaths);

        /** @var Route\Route $route */
        foreach ($this->routes as $route) {
            if ($route->auth === null) {
                $route = $route->withAuth($this->defaultAuth);
            }

            $structuredName = $route->getStructuredName();

            $formData = $this->getActionData($route->action, $formDataVocabulary);

            foreach ($route->mapToRequestBlueprint($host, $formData) as $request) {
                $schema->pushRequest($structuredName, $request);
            }
        }

        return $schema;
    }

    /**
     * Retrieves the form data associated with a specific route action, leveraging cached data when available.
     *
     * @param  RouteAction|null  $action  The route action to retrieve data for, or null if no action is defined.
     * @param  Collection  $vocabulary  The collection of vocabulary data used to process the form data.
     */
    protected function getActionData(?RouteAction $action, Collection $vocabulary): array
    {
        if ($action === null) {
            return [];
        }

        $actionName = $action->getActionFcn();

        if ($data = $this->actionDataCache->get($actionName)) {
            return $data;
        }

        return tap($action->getFormData($vocabulary), fn($data) => $this->actionDataCache->put($actionName, $data));
    }
}
