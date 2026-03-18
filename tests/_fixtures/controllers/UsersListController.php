<?php

declare(strict_types = 1);

namespace Tests\_fixtures\controllers;

use Amondar\Postman\Attributes\PostmanFormData;

/**
 * Class UsersListController
 *
 * @author Amondar-SO
 */
#[PostmanFormData([
    'my' => 'data',
])]
class UsersListController
{
    public function __invoke()
    {
        return [];
    }
}
