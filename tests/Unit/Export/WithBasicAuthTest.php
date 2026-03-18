<?php

declare(strict_types = 1);

use Amondar\Postman\Auth\Basic;

it('should properly work on basic auth:  `with full empty data`', function () {
    $routes = new Amondar\Postman\Route\RouteCollection;

    $routes->add(makeFakeRoute(
        auth: Basic::make()
    ));

    $data = Amondar\Postman\Export::from($routes, 'http://localhost/')->toArray();

    expect($data['item'][0]['request']['auth'])->toMatchArray(
        [
            'type'  => 'basic',
            'basic' => [
                [
                    'key'   => 'username',
                    'value' => '',
                    'type'  => 'string',
                ],
                [
                    'key'   => 'password',
                    'value' => '',
                    'type'  => 'string',
                ],
            ],
        ]
    );
})->group('export', 'export::auth:basic:empty');

it('should properly work on basic auth:  `with filled data`', function () {
    $routes = new Amondar\Postman\Route\RouteCollection;

    $routes->add(makeFakeRoute(
        auth: Basic::make('my', 'user')
    ));

    $data = Amondar\Postman\Export::from($routes, 'http://localhost/')->toArray();

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
})->group('export', 'export::auth:basic:filled');
