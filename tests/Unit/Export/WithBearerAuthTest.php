<?php

declare(strict_types = 1);

use Amondar\Postman\Auth\Bearer;

it('should properly work on bearer auth:  `with empty data`', function () {
    $routes = new Amondar\Postman\Route\RouteCollection;

    $routes->add(makeFakeRoute(
        auth: Bearer::make()
    ));

    $data = Amondar\Postman\Export::from($routes, 'http://localhost/')->toArray();

    expect($data['item'][0]['request']['auth'])->toMatchArray(
        [
            'type'   => 'bearer',
            'bearer' => [
                [
                    'key'   => 'token',
                    'value' => '',
                    'type'  => 'string',
                ],
            ],
        ]
    );
})->group('export', 'export::auth:bearer:empty');

it('should properly work on bearer auth:  `with filled data`', function () {
    $routes = new Amondar\Postman\Route\RouteCollection;

    $routes->add(makeFakeRoute(
        auth: Bearer::make('my-token')
    ));

    $data = Amondar\Postman\Export::from($routes, 'http://localhost/')->toArray();

    expect($data['item'][0]['request']['auth'])->toMatchArray(
        [
            'type'   => 'bearer',
            'bearer' => [
                [
                    'key'   => 'token',
                    'value' => 'my-token',
                    'type'  => 'string',
                ],
            ],
        ]
    );
})->group('export', 'export::auth:bearer:filled');
