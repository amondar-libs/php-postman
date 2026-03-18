<?php

declare(strict_types = 1);

namespace Amondar\Postman\Contracts;

use Amondar\Postman\Route\RouteCollection;

/**
 * Interface RouteParserContract
 *
 * @author Amondar-SO
 */
interface RouteParserContract
{
    public function parse(string $rootPath): RouteCollection;
}
