<?php

declare(strict_types = 1);

namespace Amondar\Postman\Blueprints;

use Illuminate\Contracts\Support\Arrayable;

/**
 * Class Request
 *
 * @implements Arrayable<string, mixed>
 *
 * @author Amondar-SO
 */
final readonly class Request implements Arrayable
{
    /**
     * Request constructor.
     */
    public function __construct(
        public string $name,
        public RequestData $data
    ) {
        //
    }

    public function toArray(): array
    {
        return [
            'name'    => $this->name,
            'request' => $this->data->toArray(),
        ];
    }
}
