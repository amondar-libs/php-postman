<?php

declare(strict_types = 1);

namespace Tests\_fixtures;

use Amondar\Postman\Contracts\Renderable;

/**
 * Class DescriptionFactory
 *
 * @author Amondar-SO
 */
class DescriptionFactory implements Renderable
{
    public function render(): array
    {
        return [
            'my' => 'factory data',
        ];
    }
}
