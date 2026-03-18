<?php

declare(strict_types = 1);

namespace Amondar\Postman\Auth;

use Illuminate\Support\Str;

/**
 * Class OAuthCodeWithPKCE
 *
 * @author Amondar-SO
 */
final readonly class OAuthCodeWithPKCE extends BaseClass
{
    public string $grantType;

    public function __construct(
        public string $callbackUrl,
        public string $authUrl,
        public string $accessTokenUrl,
        public array $scopes = [],
        public ?string $state = null,
        public string|null|int $clientId = null,
        public ?string $clientSecret = null,
        public string $tokenName = 'PKCE Token',
        public string $challengeAlgorithm = 'S256',
    ) {
        parent::__construct('oauth2');

        $this->grantType = 'authorization_code_with_pkce';
    }

    public static function make(
        string $callbackUrl,
        string $authUrl,
        string $accessTokenUrl,
        array $scopes = [],
        ?string $state = null,
        string|null|int $clientId = null,
        ?string $clientSecret = null,
        string $tokenName = 'PKCE Token',
        string $challengeAlgorithm = 'S256',
    ): OAuthCodeWithPKCE {
        return new OAuthCodeWithPKCE(
            callbackUrl: $callbackUrl,
            authUrl: $authUrl,
            accessTokenUrl: $accessTokenUrl,
            scopes: $scopes,
            state: $state,
            clientId: $clientId,
            clientSecret: $clientSecret,
            tokenName: $tokenName,
            challengeAlgorithm: $challengeAlgorithm,
        );
    }

    public function toArray(): array
    {
        return [
            'type'      => $this->type,
            $this->type => [
                [
                    'key'   => 'state',
                    'value' => $this->state ?? Str::random(40),
                    'type'  => 'string',
                ],
                [
                    'key'   => 'client_authentication',
                    'value' => 'body',
                    'type'  => 'string',
                ],
                [
                    'key'   => 'grant_type',
                    'value' => $this->grantType,
                    'type'  => 'string',
                ],
                [
                    'key'   => 'clientId',
                    'value' => $this->clientId ?? '',
                    'type'  => 'string',
                ],
                [
                    'key'   => 'clientSecret',
                    'value' => $this->clientSecret ?? '',
                    'type'  => 'string',
                ],
                [
                    'key'   => 'tokenName',
                    'value' => $this->tokenName,
                    'type'  => 'string',
                ],
                [
                    'key'   => 'challengeAlgorithm',
                    'value' => $this->challengeAlgorithm,
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
                    'value' => implode(' ', $this->scopes),
                    'type'  => 'string',
                ],
                [
                    'key'   => 'accessTokenUrl',
                    'value' => $this->accessTokenUrl,
                    'type'  => 'string',
                ],
                [
                    'key'   => 'authUrl',
                    'value' => $this->authUrl,
                    'type'  => 'string',
                ],
                [
                    'key'   => 'redirect_uri',
                    'value' => $this->callbackUrl,
                    'type'  => 'string',
                ],
            ],
        ];
    }
}
