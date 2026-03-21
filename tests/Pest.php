<?php

declare(strict_types = 1);

use Amondar\Postman\Auth\None;
use Amondar\Postman\Commands\ExportCommand;
use Amondar\Postman\Contracts\AuthenticationContract;
use Amondar\Postman\Enums\Method;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

pest()
    ->extends(Tests\WithLaravelTestCase::class)
    ->in(
        'Unit/Parsers',
        'Feature/Laravel',
    );

function readJsonFixture(string $name, bool $decode = true, bool $asLine = false): array|string
{
    $text = file_get_contents(__DIR__ . '/_fixtures/' . $name);

    if ($asLine) {
        $text = str_replace(["\r", "\n"], '', $text);
    }

    return $decode ? json_decode($text, true) : $text;
}

function makeFakeRoute(
    string $name = 'test',
    string $path = '/test',
    array $methods = ['GET'],
    ?string $domain = null,
    array $middleware = [],
    ?Amondar\Postman\Route\RouteAction $action = null,
    string $alias = 'Test name',
    Stringable|string|null $description = 'Test description',
    ?AuthenticationContract $auth = null,
    array $headers = [],
): Amondar\Postman\Route\Route {
    return new Amondar\Postman\Route\Route(
        name: $name,
        path: $path,
        methods: $methods,
        action: $action,
        domain: $domain,
        middleware: $middleware,
        alias: $alias,
        description: $description,
        headers: $headers,
        auth: $auth
    );
}

function makeFakeRequest(
    string $name = 'test',
    string $path = '/test',
    string $host = 'http://localhost',
    Method $method = Method::GET,
    array $headers = [],
    None $auth = new None,
    Stringable|string|null $description = null
): Amondar\Postman\Blueprints\Request {
    return new Amondar\Postman\Blueprints\Request(
        name: $name,
        data: new Amondar\Postman\Blueprints\RequestData(
            path: $path,
            host: $host,
            method: $method,
            headers: $headers,
            auth: $auth,
            description: $description
        )
    );
}

function makeExportCommandTester(): CommandTester
{
    $command = new ExportCommand;
    $application = new Application;
    $application->addCommand($command);

    return new CommandTester($application->find('export'));
}
