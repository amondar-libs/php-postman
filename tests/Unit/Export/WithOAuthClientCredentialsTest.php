<?php

declare(strict_types = 1);

use Amondar\Postman\Auth\OAuthClientCredentials;

it('should properly work on oauth2 client credentials auth:  `with empty data`', function () {
    $routes = new Amondar\Postman\Route\RouteCollection;

    $routes->add(makeFakeRoute(
        auth: OAuthClientCredentials::make()
    ));

    $data = Amondar\Postman\Export::from($routes, 'http://localhost/')->toArray();

    expect($data['item'][0]['request']['auth'])->toMatchArray(
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
                    'value' => 'client_credentials',
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
                    'value' => 'Client Token',
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
            ],
        ]
    );
})->group('export', 'export::auth:oauth2:client_credentials:empty');

it('should properly work on oauth2 client credentials auth:  `with filled data`', function () {
    $routes = new Amondar\Postman\Route\RouteCollection;

    $routes->add(makeFakeRoute(
        auth: OAuthClientCredentials::make('client-id', 'client-secret', ['scope1', 'scope2'])
    ));

    $data = Amondar\Postman\Export::from($routes, 'http://localhost/')
        ->useOAuthTokenPath('/oauth/token')
        ->toArray();

    expect($data[ 'variable' ])->toBeArray()->toHaveCount(2)
        ->and($data[ 'variable' ][ 1 ])->toMatchArray([
            'key'   => 'oauth_full_url',
            'value' => '{{base_url}}/oauth/token',
        ])
        ->and($data[ 'item' ][ 0 ][ 'request' ][ 'auth' ])->toMatchArray(
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
                        'value' => 'client_credentials',
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
                        'value' => 'Client Token',
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
                ],
            ]
        );
})->group('export', 'export::auth:oauth2:client_credentials:filled');
