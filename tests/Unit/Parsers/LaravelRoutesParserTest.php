<?php

declare(strict_types = 1);

use Amondar\Postman\Auth\Basic;
use Tests\_fixtures\controllers\UsersListController;

it('should parse laravel routes properly', function () {
    expect($this->routes->where('path', 'api/v1/users'))->not->toBeEmpty()
        ->first()->toEqual(makeFakeRoute(
            name: 'api.v1.users.list',
            path: 'api/v1/users',
            methods: ['GET', 'HEAD'],
            middleware: ['api', 'auth:api'],
            action: new Amondar\Postman\Route\RouteAction(
                UsersListController::class,
                '__invoke'
            ),
            alias: 'Users list',
            description: '>Here is a simple inline markdown description',
            auth: Basic::make('my', 'user')
        ));
})->group('parser', 'parser::laravel');
