<?php

declare(strict_types = 1);

use Amondar\Postman\Auth\OAuthPassword;

it('should properly work on oauth2 password auth:  `with empty data`', function () {
    $routes = new Amondar\Postman\Route\RouteCollection;

    $routes->add(makeFakeRoute(
        auth: OAuthPassword::make()
    ));

    $data = Amondar\Postman\Export::from($routes, 'http://localhost/')->toArray();

    expect($data[ 'item' ][ 0 ][ 'request' ][ 'auth' ])->toMatchArray(
        [
            'type'   => 'oauth2',
            'oauth2' => [
                [
                    'key'   => 'accessTokenUrl',
                    'value' => '{{oauth_full_url}}',
                    'type'  => 'string',
                ],
                [
                    'key'   => 'client_authentication',
                    'value' => 'body',
                    'type'  => 'string',
                ],
                [
                    'key'   => 'grant_type',
                    'value' => 'password',
                    'type'  => 'string',
                ],
                [
                    'key'   => 'clientId',
                    'value' => '',
                    'type'  => 'string',
                ],
                [
                    'key'   => 'clientSecret',
                    'value' => '',
                    'type'  => 'string',
                ],
                [
                    'key'   => 'tokenName',
                    'value' => 'Passwords Token',
                    'type'  => 'string',
                ],
                [
                    'key'   => 'challengeAlgorithm',
                    'value' => 'S256',
                    'type'  => 'string',
                ],
                [
                    'key'   => 'addTokenTo',
                    'value' => 'header',
                    'type'  => 'string',
                ],
                [
                    'key'   => 'tokenType',
                    'value' => 'Bearer',
                    'type'  => 'string',
                ],
                [
                    'key'   => 'scope',
                    'value' => '',
                    'type'  => 'string',
                ],
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
})->group('export', 'export::auth:oauth2:password:empty');

it('should properly work on oauth2 password auth:  `with filled data`', function () {
    $routes = new Amondar\Postman\Route\RouteCollection;

    $routes->add(makeFakeRoute(
        auth: OAuthPassword::make('client-id', 'client-secret', 'my-user', 'my-pass', [ 'scope1', 'scope2' ])
    ));

    $data = Amondar\Postman\Export::from($routes, 'http://localhost/')->toArray();

    expect($data[ 'item' ][ 0 ][ 'request' ][ 'auth' ])->toMatchArray(
        [
            'type'   => 'oauth2',
            'oauth2' => [
                [
                    'key'   => 'accessTokenUrl',
                    'value' => '{{oauth_full_url}}',
                    'type'  => 'string',
                ],
                [
                    'key'   => 'client_authentication',
                    'value' => 'body',
                    'type'  => 'string',
                ],
                [
                    'key'   => 'grant_type',
                    'value' => 'password',
                    'type'  => 'string',
                ],
                [
                    'key'   => 'clientId',
                    'value' => 'client-id',
                    'type'  => 'string',
                ],
                [
                    'key'   => 'clientSecret',
                    'value' => 'client-secret',
                    'type'  => 'string',
                ],
                [
                    'key'   => 'tokenName',
                    'value' => 'Passwords Token',
                    'type'  => 'string',
                ],
                [
                    'key'   => 'challengeAlgorithm',
                    'value' => 'S256',
                    'type'  => 'string',
                ],
                [
                    'key'   => 'addTokenTo',
                    'value' => 'header',
                    'type'  => 'string',
                ],
                [
                    'key'   => 'tokenType',
                    'value' => 'Bearer',
                    'type'  => 'string',
                ],
                [
                    'key'   => 'scope',
                    'value' => 'scope1 scope2',
                    'type'  => 'string',
                ],
                [
                    'key'   => 'username',
                    'value' => 'my-user',
                    'type'  => 'string',
                ],
                [
                    'key'   => 'password',
                    'value' => 'my-pass',
                    'type'  => 'string',
                ],
            ],
        ]
    );
})->group('export', 'export::auth:oauth2:password:filled');
