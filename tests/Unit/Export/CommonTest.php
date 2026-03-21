<?php

declare(strict_types = 1);

use Amondar\Postman\Auth\Basic;

use function Spatie\PestPluginTestTime\testTime;

beforeEach(fn() => testTime()->freeze('2026-01-06 20:00:00'));

it('should:  `export common routes to array`', function () {
    $routes = new Amondar\Postman\Route\RouteCollection;

    $routes->add(makeFakeRoute(
        'api.v1.test',
        '/api/v1/test'
    ));

    $data = Amondar\Postman\Export::from($routes, 'http://localhost/')->toArray();

    expect($data)->toMatchArray(readJsonFixture('common_export_result.json'));
})
    ->group('export', 'export::array:common')
    ->note('Export common routes to array and compare with fixture');

it('should:  `export with default auth`', function () {
    $routes = new Amondar\Postman\Route\RouteCollection;

    $routes->add(makeFakeRoute());

    $data = Amondar\Postman\Export::from($routes, 'http://localhost/')->useDefaultAuth(Basic::make('my', 'user'))->toArray();

    expect($data['item'][0]['request']['auth'])->toMatchArray(
        [
            'type'  => 'basic',
            'basic' => [
                [
                    'key'   => 'username',
                    'value' => 'my',
                    'type'  => 'string',
                ],
                [
                    'key'   => 'password',
                    'value' => 'user',
                    'type'  => 'string',
                ],
            ],
        ]
    );
})
    ->group('export', 'export::array:common')
    ->note('Create a route without auth completely. Export and set the default global auth type.');

it('should:  `export with global headers`', function () {
    $routes = new Amondar\Postman\Route\RouteCollection;

    $routes->add(makeFakeRoute());

    $data = Amondar\Postman\Export::from($routes, 'http://localhost/')->withGlobalHeaders([
        'Accept' => 'application/json',
    ])->toArray();

    expect($data['item'][0]['request']['header'])->toMatchArray(
        [
            [
                'key'   => 'Accept',
                'value' => 'application/json',
                'type'  => 'text',
            ],
        ]
    );
})
    ->group('export', 'export::array:common')
    ->note('Export and set the headers globally. Extend route ones.');
