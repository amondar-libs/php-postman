<?php

declare(strict_types = 1);

namespace Amondar\Postman\Blueprints;

use Illuminate\Contracts\Support\Arrayable;

/**
 * Class Variable
 *
 * @implements Arrayable<string, string|int>
 *
 * @author Amondar-SO
 */
final readonly class Variable implements Arrayable
{
    /**
     * Variable constructor.
     */
    public function __construct(
        public string $key,
        public string|int $value
    ) {
        //
    }

    /**
     * Get the instance as an array.
     *
     * @return array<string, string|int>
     */
    public function toArray(): array
    {
        return [
            'key'        => $this->key,
            'value'      => $this->value,
        ];
    }
}
