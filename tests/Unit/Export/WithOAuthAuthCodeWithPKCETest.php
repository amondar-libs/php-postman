<?php

declare(strict_types = 1);

use Amondar\Postman\Auth\OAuthCodeWithPKCE;

it('should properly work on oauth2 auth code PKCE auth', function () {
    $routes = new Amondar\Postman\Route\RouteCollection;

    $routes->add(makeFakeRoute(
        auth: OAuthCodeWithPKCE::make(
            '/asd',
            '/asd',
            '/asd',
            [ 'scope1', 'scope2' ],
            'state',
            'client-id',
            'client-secret',
            'My token name here'
        )
    ));

    $data = Amondar\Postman\Export::from($routes, 'http://localhost/')->toArray();

    expect($data[ 'item' ][ 0 ][ 'request' ][ 'auth' ])->toMatchArray(
        [
            'type'   => 'oauth2',
            'oauth2' => [
                [
                    'key'   => 'state',
                    'value' => 'state',
                    'type'  => 'string',
                ],
                [
                    'key'   => 'client_authentication',
                    'value' => 'body',
                    'type'  => 'string',
                ],
                [
                    'key'   => 'grant_type',
                    'value' => 'authorization_code_with_pkce',
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
                    'value' => 'My token name here',
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
                    'key'   => 'accessTokenUrl',
                    'value' => '/asd',
                    'type'  => 'string',
                ],
                [
                    'key'   => 'authUrl',
                    'value' => '/asd',
                    'type'  => 'string',
                ],
                [
                    'key'   => 'redirect_uri',
                    'value' => '/asd',
                    'type'  => 'string',
                ],
            ],
        ]
    );
})->group('export', 'export::auth:oauth2:auth-code-with-pkce');
