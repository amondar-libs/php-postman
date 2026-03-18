<?php

declare(strict_types = 1);

namespace Amondar\Postman\Auth;

/**
 * Class OAuthClientCredentials
 *
 * @author Amondar-SO
 */
final readonly class OAuthClientCredentials extends BaseClass
{
    public string $grantType;

    public function __construct(
        public string|int|null $clientId = null,
        public ?string $clientSecret = null,
        public array $scopes = [],
        public string $tokenName = 'Client Token',
    ) {
        parent::__construct('oauth2');

        $this->grantType = 'client_credentials';
    }

    public static function make(
        string|int|null $clientId = null,
        ?string $clientSecret = null,
        array $scopes = [],
        string $tokenName = 'Client Token',
    ): OAuthClientCredentials {
        return new OAuthClientCredentials($clientId, $clientSecret, $scopes, $tokenName);
    }

    public function toArray(): array
    {
        return [
            'type'      => $this->type,
            $this->type => [
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
                    'value' => implode(' ', $this->scopes),
                    'type'  => 'string',
                ],
            ],
        ];
    }
}
