<?php

declare(strict_types = 1);

namespace Amondar\Postman\Enums;

use Illuminate\Support\Collection;

enum Method: string
{
    case GET = 'GET';
    case POST = 'POST';
    case PUT = 'PUT';
    case PATCH = 'PATCH';
    case DELETE = 'DELETE';
    case OPTIONS = 'OPTIONS';
    case HEAD = 'HEAD';

    public static function fromString(string $method): Method
    {
        return self::from(mb_strtoupper($method));
    }

    public function allowed(): bool
    {
        return in_array($this, [ Method::GET, Method::POST, Method::PUT, Method::PATCH, Method::DELETE, Method::OPTIONS ]);
    }

    public function renderForRequest(array $request, array $data): array
    {
        if ($data === []) {
            return $request;
        }

        return match ($this) {
            Method::GET, Method::OPTIONS, Method::HEAD => [
                ...$request,
                'url' => array_merge($request['url'], $this->renderDataAsQuery($data, $request['url']['raw'])),
            ],
            default => array_merge($request, $this->renderDataAsBody($data)),
        };
    }

    public function renderDataAsQuery(array $data, string $rawUrl): array
    {
        $query = urldecode(http_build_query($data));

        return [
            'raw'   => $rawUrl . '?' . $query,
            'query' => (new Collection($data))
                ->map(fn($value, $key) => [ 'key' => $key, 'value' => $value ])
                ->values()
                ->all(),
        ];
    }

    public function renderDataAsBody(array $data): array
    {
        return [
            'body' => [
                'mode'    => 'raw',
                'raw'     => json_encode($data, JSON_PRETTY_PRINT),
                'options' => [
                    'raw' => [
                        'language' => 'json',
                    ],
                ],
            ],
        ];
    }
}
