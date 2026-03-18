<?php

declare(strict_types = 1);

namespace Amondar\Postman\Blueprints;

use Illuminate\Contracts\Support\Arrayable;

/**
 * Class Header
 *
 * @author Amondar-SO
 */
final readonly class Header implements Arrayable
{
    public function __construct(
        public string $key,
        public string|int $value,
        public string $type = 'text'
    ) {
        //
    }

    public function toArray(): array
    {
        return [
            'key'   => $this->key,
            'value' => $this->value,
            'type'  => $this->type,
        ];
    }
}
