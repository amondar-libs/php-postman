<?php

declare(strict_types = 1);

namespace Amondar\Postman\Auth;

/**
 * Class OAuthPassword
 *
 * @author Amondar-SO
 */
final readonly class OAuthPassword extends BaseClass
{
    public string $grantType;

    public function __construct(
        public string|int|null $clientId = null,
        public ?string $clientSecret = null,
        public ?string $username = null,
        public ?string $password = null,
        public array $scopes = [],
        public string $tokenName = 'Passwords Token',
    ) {
        parent::__construct('oauth2');

        $this->grantType = 'password';
    }

    public static function make(
        string|int|null $clientId = null,
        ?string $clientSecret = null,
        ?string $username = null,
        ?string $password = null,
        array $scopes = [],
        string $tokenName = 'Passwords Token',
    ): OAuthPassword {
        return new OAuthPassword($clientId, $clientSecret, $username, $password, $scopes, $tokenName);
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
                [
                    'key'   => 'username',
                    'value' => $this->username ?? '',
                    'type'  => 'string',
                ],
                [
                    'key'   => 'password',
                    'value' => $this->password ?? '',
                    'type'  => 'string',
                ],
            ],
        ];
    }
}
