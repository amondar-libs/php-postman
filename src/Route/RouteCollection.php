<?php

declare(strict_types = 1);

namespace Amondar\Postman\Route;

use Illuminate\Support\Collection;

/**
 * Class RouteCollection
 *
 * @extends Collection<int, Route>
 *
 * @author Amondar-SO
 */
final class RouteCollection extends Collection
{
    /**
     * Push one or more items onto the end of the collection.
     *
     * @param  Route  ...$values
     * @return static
     */
    public function push(...$values)
    {
        foreach ($values as $value) {
            $this->items[] = $value;
        }

        return $this;
    }

    /**
     * Apply the given route filter to the collection.
     *
     * @return static
     */
    public function filterRoutes(RouteFilter $filter)
    {
        return $this->filter(fn(Route $route) => $filter->test($route));
    }
}
