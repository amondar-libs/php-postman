<?php

declare(strict_types = 1);

namespace Amondar\Postman\Auth;

use Amondar\Postman\Contracts\AuthenticationContract;
use Illuminate\Contracts\Support\Arrayable;

/**
 * Class BaseClass
 *
 * @internal
 *
 * @author Amondar-SO
 */
abstract readonly class BaseClass implements Arrayable, AuthenticationContract
{
    /**
     * BaseClass constructor.
     */
    public function __construct(public string $type)
    {
        //
    }

    abstract public function toArray(): array;
}
