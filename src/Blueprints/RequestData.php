<?php

declare(strict_types = 1);

namespace Amondar\Postman\Blueprints;

use Amondar\Postman\Auth\None;
use Amondar\Postman\Contracts\AuthenticationContract;
use Amondar\Postman\Enums\Method;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Stringable;

/**
 * Class RequestData
 *
 * @implements Arrayable<string, mixed>
 *
 * @author Amondar-SO
 */
final readonly class RequestData implements Arrayable
{
    /**
     * RequestData constructor.
     */
    public function __construct(
        public string $path,
        public string $host,
        public Method $method,
        public Collection $headers = new Collection,
        public AuthenticationContract $auth = new None,
        public Stringable|string|null $description = null,
        public array $formData = [],
    ) {
        //
    }

    public function toArray(): array
    {
        $host = $this->prepareHost();
        $path = mb_ltrim($this->path, '/');

        $request = [
            'method'      => $this->method->value,
            'header'      => $this->headers->toArray(),
            'auth'        => $this->auth->toArray(),
            'url'         => [
                'raw'  => "{$host[ 'scheme' ]}{$host['host']}/$path",
                'host' => [ $host['host'] ],
                'path' => explode('/', $path),
            ],
            'description' => $this->normalizeDescription(),
        ];

        return $this->method->renderForRequest($request, $this->formData);
    }

    private function prepareHost(): array
    {
        $host = parse_url($this->host);

        if ( ! is_array($host)) {
            return [
                'scheme' => '',
                'host'   => $this->host,
            ];
        }

        if (count($host) === 1 && array_key_exists('path', $host)) {
            return [
                'scheme' => '',
                'host'   => $host['path'],
            ];
        }

        if (array_key_exists('scheme', $host)) {
            $host[ 'scheme' ] = "{$host[ 'scheme' ]}://";
        }

        return $host;

    }

    private function normalizeDescription(): string
    {
        if ($this->description instanceof Stringable) {
            return $this->description->__toString();
        }

        return $this->description ?? '';
    }
}
