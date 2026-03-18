<?php

declare(strict_types = 1);

namespace Amondar\Postman\Auth;

/**
 * Class None
 *
 * @author Amondar-SO
 */
final readonly class None extends BaseClass
{
    public function __construct()
    {
        parent::__construct('none');
    }

    public static function make(): None
    {
        return new None;
    }

    public function toArray(): array
    {
        return [];
    }
}
