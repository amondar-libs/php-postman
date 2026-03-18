<?php

declare(strict_types = 1);

namespace Amondar\Postman\Auth;

/**
 * Class Bearer
 *
 * @author Amondar-SO
 */
final readonly class Bearer extends BaseClass
{
    public function __construct(
        public ?string $token = null
    ) {
        parent::__construct('bearer');
    }

    public static function make(?string $token = null): Bearer
    {
        return new Bearer($token);
    }

    public function toArray(): array
    {
        return [
            'type'      => $this->type,
            $this->type => [
                [
                    'key'   => 'token',
                    'value' => $this->token ?? '',
                    'type'  => 'string',
                ],
            ],
        ];
    }
}
