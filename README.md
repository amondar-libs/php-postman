# PHP Postman

[![PHP Version](https://img.shields.io/badge/php-%5E8.3-blue)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/laravel-10%20%7C%2011%20%7C%2012-red)](https://laravel.com/)

A PHP package that generates [Postman](https://www.postman.com/) collections from your application routes. It supports
Laravel out of the box and can be extended to work with any PHP framework.

## Table of Contents

- [Installation](#installation)
- [Basic Usage](#basic-usage)
    - [Laravel Integration](#laravel-integration)
    - [Standalone CLI](#standalone-cli)
    - [Programmatic Usage](#programmatic-usage)
- [Deep Dive](#deep-dive)
    - [Authentication](#authentication)
    - [Route Macros (Laravel)](#route-macros-laravel)
    - [Route Filtering](#route-filtering)
    - [Form Data Attributes](#form-data-attributes)
    - [Descriptions](#descriptions)
    - [CLI Options Reference](#cli-options-reference)
- [Extending the Package](#extending-the-package)
    - [Custom Route Parser](#custom-route-parser)
    - [Custom Authentication](#custom-authentication)
    - [Custom Form Data Renderable](#custom-form-data-renderable)

---

## Installation

Install the package via Composer:

```bash
composer require amondar-libs/php-postman
```

## Getting started

The below example shows how to export a Postman collection from Laravel routes.
But you can integrate this into your none-Laravel application by creating your RoutesParser class.

```php
class ExportPostmanCollectionController
{
    public function __invoke(Router $router)
    {
        $export = cache()
            ->store('file')
            ->rememberForever('postman-collection', fn() => [
                'name'    => $name = now()->format('Y-m-d-H-i-s') . '-postman-collection.json',
                'content' => $this->export($router, $name),
            ]);

        return response()->streamDownload(
            function () use ($export) {
                echo $export['content'];
            },
            $export['name'],
            [
                'Content-Type' => 'application/json',
            ]
        );
    }

    private function export(RouteCollection $router, string $name): string
    {
        $routes = (new LaravelRoutesParser)->parseLaravelRoutes($router->getRoutes());

        return Export::from($routes->filterRoutes(
            RouteFilter::apply()
                ->affectedByPackage()
                ->byMiddleware('api')
        ), url('/'))
            ->parseDataIn(app_path())
            ->useOAuthTokenPath('/oauth/token')
            ->useDefaultAuth(
                new OAuthCodeWithPKCE(
                    callbackUrl: '{{base_url}}/auth/v1/callback',
                    authUrl: '{{base_url}}/oauth/authorize',
                    accessTokenUrl: '{{oauth_full_url}}',
                    state: 'development-state',
                    clientId: '{{postman_pkce_client_id}}',
                    tokenName: config('app.name') . ' PKCE Token'
                )
            )
            ->withGlobalVariables([
                'postman_pkce_client_id' => config('project.authentication.postman_pkce_client_id', ''),
            ])
            ->toJson($name);
    }
}
```

> Remember to add the optimize command in AppServiceProvider to clear the cache after each update in docker (of course if you're using it).

### Requirements

- PHP 8.3 or higher
- Laravel 10, 11, or 12 (for Laravel integration)

### Laravel Auto-Discovery

The package ships with a Laravel service provider that is auto-discovered. No manual registration is needed. The service
provider registers route macros (`alias`, `auth`, `description`, `additionalHeaders`, `structureDepth`) that you can use
directly on your route definitions.

---

## Basic Usage

### Laravel Integration

The quickest way to export your Laravel routes to a Postman collection is via the built-in CLI binary:

```bash
./vendor/bin/postman export --laravel --base-url=https://api.example.com --output=postman.json
```

This will bootstrap your Laravel application, parse all registered routes, and write a Postman collection JSON file.

### Standalone CLI

For non-Laravel projects, you can provide a custom route parser class:

```bash
./vendor/bin/postman export \
    --routes-parser="App\\Documentation\\MyRoutesParser" \
    --base-url=https://api.example.com \
    --output=postman.json
```

If `--routes-parser` is not provided and `--laravel` is not set, the CLI will interactively ask for the parser class
name.

### Programmatic Usage

You can also generate collections programmatically in your PHP code:

```php
use Amondar\Postman\Auth\Bearer;use Amondar\Postman\Export;use Amondar\Postman\Route\Route;use Amondar\Postman\Route\RouteCollection;

// Build a route collection
$routes = new RouteCollection();
$routes->push(new Route(
    name: 'users.index',
    path: 'api/users',
    methods: ['GET'],
    action: null,
));
$routes->push(new Route(
    name: 'users.store',
    path: 'api/users',
    methods: ['POST'],
    action: null,
));

// Export to JSON
$json = Export::from($routes, 'https://api.example.com')
    ->useDefaultAuth(Bearer::make('your-token'))
    ->withGlobalHeaders(['MY-HEADER' => 'my-value'])
    ->withGlobalVariables(['my_var' => 'my-value'])
    ->toJson('My API Collection');

file_put_contents('postman.json', $json);
```

#### Using with Laravel Routes Parser

```php
use Amondar\Postman\Export;
use Amondar\Postman\Laravel\LaravelRoutesParser;

$parser = new LaravelRoutesParser();
$routes = $parser->parseLaravelRoutes(app('router')->getRoutes());

$json = Export::from($routes, config('app.url'))
    ->toJson('My Laravel API');
```

---

## Deep Dive

### Authentication

The package supports multiple authentication types that map directly to Postman's authentication options. Set a default
auth for the entire collection or assign auth per route.

#### Bearer Token

```php
use Amondar\Postman\Auth\Bearer;

$export = Export::from($routes, $baseUrl)
    ->useDefaultAuth(Bearer::make('optional-default-token'));
```

#### Basic Auth

```php
use Amondar\Postman\Auth\Basic;

$export = Export::from($routes, $baseUrl)
    ->useDefaultAuth(Basic::make('username', 'password'));
```

#### OAuth 2.0 — Password Grant

```php
use Amondar\Postman\Auth\OAuthPassword;

$export = Export::from($routes, $baseUrl)
    ->useDefaultAuth(new OAuthPassword(
        clientId: 'your-client-id',
        clientSecret: 'your-client-secret',
        scope: 'read write',
    ))
    ->useOAuthTokenPath('/oauth/token');
```

#### OAuth 2.0 — Client Credentials

```php
use Amondar\Postman\Auth\OAuthClientCredentials;

$export = Export::from($routes, $baseUrl)
    ->useDefaultAuth(new OAuthClientCredentials(
        clientId: 'your-client-id',
        clientSecret: 'your-client-secret',
        scope: 'read write',
    ))
    ->useOAuthTokenPath('/oauth/token');
```

#### OAuth 2.0 — Authorization Code with PKCE

```php
use Amondar\Postman\Auth\OAuthCodeWithPKCE;

$export = Export::from($routes, $baseUrl)
    ->useDefaultAuth(new OAuthCodeWithPKCE(
        clientId: 'your-client-id',
        callbackUrl: 'https://app.example.com/callback',
        authUrl: 'https://auth.example.com/authorize',
        scope: 'openid profile',
    ))
    ->useOAuthTokenPath('/oauth/token');
```

#### No Authentication

```php
use Amondar\Postman\Auth\None;

// This is the default — routes with no auth assigned will use None
$export = Export::from($routes, $baseUrl)
    ->useDefaultAuth(new None());
```

#### Per-Route Authentication (Laravel)

When using Laravel, you can set authentication on individual routes or route groups:

```php
use Amondar\Postman\Auth\Bearer;
use Amondar\Postman\Auth\Basic;

// Single route
Route::get('/api/users', [UserController::class, 'index'])
    ->name('users.index')
    ->auth(Bearer::make());

// Route group
Route::auth(Basic::make())->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])
        ->name('admin.dashboard');
});
```

### Route Macros (Laravel)

The service provider registers several macros on Laravel's `Route`, `RouteRegistrar`, and `Router` classes:

#### `alias(string $alias)`

Override the display name of a route in the Postman collection:

```php
Route::get('/api/v2/users', [UserController::class, 'index'])
    ->name('api.v2.users.index')
    ->alias('List All Users');
```

#### `description(Stringable|string $description)`

Add a description to the route. Accepts a plain string, a `\Stringable` instance, or a class name that implements
`Stringable`:

```php
Route::get('/api/users', [UserController::class, 'index'])
    ->name('users.index')
    ->description('Returns a paginated list of all users.');
```

#### `structureDepth(int $depth)`

Control how deeply the route name is used for folder nesting in the Postman collection. By default, all segments except
the last one are used as folder names:

```php
// Route name: "api.v2.users.index"
// Default folders: api > v2 > users
// With structureDepth(0): api
// With structureDepth(1): api > v2
Route::get('/api/v2/users', [UserController::class, 'index'])
    ->name('api.v2.users.index')
    ->structureDepth(2);
```

#### `auth(AuthenticationContract $type)`

Set the authentication type for a route or route group (see [Authentication](#authentication)).

#### `additionalHeaders(array $headers)`

Add custom headers to the Postman request:

```php
Route::get('/api/users', [UserController::class, 'index'])
    ->name('users.index')
    ->additionalHeaders([
        'X-Custom-Header' => 'custom-value',
        'Accept-Language' => 'en',
    ]);
```

### Route Filtering

Filter which routes are included in the exported collection. Filtering is available both via CLI options and
programmatically.

#### CLI Filtering

```bash
# Filter by path pattern
./vendor/bin/postman export --laravel --route-paths="api/*"

# Filter by route name
./vendor/bin/postman export --laravel --route-names="users.*"

# Filter by HTTP method. Case-insensitive.
./vendor/bin/postman export --laravel --route-methods="GET;POST"

# Filter by middleware
./vendor/bin/postman export --laravel --route-middlewares="auth:*"

# Combine multiple filters (all conditions must match)
./vendor/bin/postman export --laravel \
    --route-paths="api/*" \
    --route-methods="GET" \
    --route-middlewares="auth"
```

Multiple values can be separated by semicolons or passed as repeated options.

#### Programmatic Filtering

```php
use Amondar\Postman\Route\RouteFilter;

$filter = RouteFilter::apply()
    ->byPath('api/*')
    ->byMethod(['GET', 'POST'])
    ->byName('users.*')
    ->byMiddleware('auth');

$filteredRoutes = $routes->filterRoutes($filter)->values();

$json = Export::from($filteredRoutes, $baseUrl)->toJson();
```

### Form Data Attributes

Use the `#[PostmanFormData]` attribute to define request body data for your routes. The attribute can be applied to
controller classes or methods and accepts either an inline array or a class implementing `Renderable`.

#### Inline Array

```php
use Amondar\Postman\Attributes\PostmanFormData;

class UserController
{
    #[PostmanFormData([
        'name' => 'John Doe',
        'email' => 'john@example.com'
    ])]
    public function store(Request $request)
    {
        // ...
    }
}
```

#### Using a Renderable Class

```php
use Amondar\Postman\Attributes\PostmanFormData;
use Amondar\Postman\Contracts\Renderable;

class StoreUserFormData implements Renderable
{
    public function render(): array
    {
        // There can be complex logic to generate form data.
        // Also, you can use faker here.
        return [
            'name'  => 'John Doe',
            'email' => 'john@example.com',
            'role'  => 'admin',
        ];
    }
}

class UserController
{
    #[PostmanFormData(StoreUserFormData::class)]
    public function store(Request $request)
    {
        // ...
    }
}
```

To enable attribute scanning, specify the directories to scan using `parseDataIn()`:

```php
$json = Export::from($routes, $baseUrl)
    ->parseDataIn('app/Http/Controllers', 'app/Http/Requests')
    ->toJson();
```

Or via CLI:

```bash
./vendor/bin/postman export --laravel --attributes-in=app/Domain --attributes-in=app/Infrastructure
```

### Descriptions

Route descriptions support both plain strings and `Stringable` objects. This allows integration with markdown libraries
or any custom description generator:

```php
use Amondar\Postman\Contracts\Renderable;

// Plain string
Route::get('/api/users', [UserController::class, 'index'])
    ->description('Returns a list of users.');

// Stringable class (e.g., from amondar-libs/php-markdown)
Route::get('/api/users', [UserController::class, 'index'])
    ->description(Markdown::line('Returns a **list** of users.'));

// Class name that implements Stringable — will be instantiated automatically
Route::get('/api/users', [UserController::class, 'index'])
    ->description(UserIndexDescription::class);
```

### CLI Options Reference

| Option                     | Alias            | Description                                                                                                                                     |
|----------------------------|------------------|-------------------------------------------------------------------------------------------------------------------------------------------------|
| `--laravel`                | —                | Bootstrap and parse a Laravel application                                                                                                       |
| `--routes-parser`          | `-parser`        | Fully qualified class name of a custom route parser                                                                                             |
| `--attributes-in`          | `-attributes`    | Directories to scan for `PostmanFormData` attributes (repeatable)                                                                               |
| `--collection-name`        | `-name`          | Name of the Postman collection (default: `postman.json`)                                                                                        |
| `--collection-description` | `-description`   | Description of the Postman collection                                                                                                           |
| `--base-url`               | `-url`           | Base URL for requests, stored as `{{base_url}}` variable                                                                                        |
| `--oauth-token-url`        | `-oauth-url`     | OAuth token endpoint, stored as `{{oauth_full_url}}` variable                                                                                   |
| `--headers`                | `-H`             | Additional headers to be sent with each request. Example: --headers="X-Custom-Header: my-custom-header".                                        |
| `--variables`              | `-VR`            | Additional variables to be sent on collection. Example: --variables="my_var: my-value". Then in the collection you can use {{my_var}} variable. |
| `--route-paths`            | `-paths`         | Filter routes by path patterns (semicolon-separated)                                                                                            |
| `--route-names`            | `-names`         | Filter routes by name patterns (semicolon-separated)                                                                                            |
| `--route-methods`          | `-methods`       | Filter routes by HTTP methods (semicolon-separated)                                                                                             |
| `--route-middlewares`      | `-middlewares`   | Filter routes by middleware (semicolon-separated)                                                                                               |
| `--route-affected-only`    | `-affected-only` | Filter routes that are affected by the package. E.g. routes that have package attributes - alias, description, etc.                             |
| `--output`                 | —                | File path to write the collection to (prints to stdout if omitted)                                                                              |
| `--format`                 | —                | Output format: `json` (default) or `pretty-json`                                                                                                |

---

## Extending the Package

### Custom Route Parser

To support frameworks other than Laravel, implement the `RouteParserContract` interface:

```php
use Amondar\Postman\Contracts\RouteParserContract;
use Amondar\Postman\Route\Route;
use Amondar\Postman\Route\RouteCollection;

class SymfonyRoutesParser implements RouteParserContract
{
    public function parse(string $rootPath): RouteCollection
    {
        $collection = new RouteCollection();

        // Parse your framework's routes and convert them
        foreach ($this->getSymfonyRoutes($rootPath) as $sfRoute) {
            $collection->push(new Route(
                name: $sfRoute->getName(),
                path: $sfRoute->getPath(),
                methods: $sfRoute->getMethods(),
                action: null, // or create a RouteAction if applicable
                middleware: [],
            ));
        }

        return $collection;
    }
}
```

Use it via CLI:

```bash
./vendor/bin/postman export \
    --routes-parser="App\\Documentation\\SymfonyRoutesParser" \
    --base-url=https://api.example.com
```

Or programmatically:

```php
$parser = new SymfonyRoutesParser();
$routes = $parser->parse('/path/to/project/can/be/ignored');

$json = Export::from($routes, 'https://api.example.com')->toJson();
```

### Custom Authentication

Create your own authentication type by implementing the `AuthenticationContract` interface:

```php
use Amondar\Postman\Contracts\AuthenticationContract;

class ApiKeyAuth implements AuthenticationContract
{
    public function __construct(
        public readonly string $headerName = 'X-API-Key',
        public readonly ?string $value = null,
    ) {}

    public function toArray(): array
    {
        return [
            'type'   => 'apikey',
            'apikey' => [
                [
                    'key'   => 'key',
                    'value' => $this->headerName,
                    'type'  => 'string',
                ],
                [
                    'key'   => 'value',
                    'value' => $this->value ?? '',
                    'type'  => 'string',
                ],
                [
                    'key'   => 'in',
                    'value' => 'header',
                    'type'  => 'string',
                ],
            ],
        ];
    }
}
```

Then use it as default auth or per-route:

```php
$export = Export::from($routes, $baseUrl)
    ->useDefaultAuth(new ApiKeyAuth('X-API-Key', 'secret'));

// Or in Laravel routes:
Route::get('/api/data', [DataController::class, 'index'])
    ->auth(new ApiKeyAuth());
```

### Custom Form Data Renderable

Implement the `Renderable` interface to create reusable form data definitions:

```php
use Amondar\Postman\Contracts\Renderable;

class PaginationFormData implements Renderable
{
    public function render(): array
    {
        return [
            'page' => 1,
            'per_page' => 15,
            'sort_by' => 'created_at',
            'sort_order' => 'desc',
        ];
    }
}
```

Then reference it in the `PostmanFormData` attribute:

```php
use Amondar\Postman\Attributes\PostmanFormData;

class UserController
{
    #[PostmanFormData(PaginationFormData::class)]
    public function index()
    {
        // ...
    }
}
```

---

## License

Please see [LICENSE.md](LICENSE.md) for details.
