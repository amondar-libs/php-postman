<?php

declare(strict_types = 1);

namespace Amondar\Postman\Auth;

/**
 * Class Basic
 *
 * @author Amondar-SO
 */
final readonly class Basic extends BaseClass
{
    public function __construct(
        public ?string $username = null,
        public ?string $password = null
    ) {
        parent::__construct('basic');
    }

    public static function make(?string $username = null, ?string $password = null): Basic
    {
        return new Basic($username, $password);
    }

    public function toArray(): array
    {
        return [
            'type'      => $this->type,
            $this->type => [
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
