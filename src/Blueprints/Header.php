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

    public static function fromSimpleArray(array $headers): array
    {
        if ($headers === []) {
            return [];
        }

        $keys = array_keys($headers);
        $values = array_values($headers);

        return array_map(fn($key, $value) => new self($key, $value), $keys, $values);
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
