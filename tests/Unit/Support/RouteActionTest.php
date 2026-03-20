<?php

declare(strict_types = 1);

use Amondar\ClassAttributes\Parse;
use Amondar\Postman\Attributes\PostmanFormData;
use Amondar\Postman\Route\RouteAction;
use Tests\_fixtures\controllers\PostController;
use Tests\_fixtures\controllers\UsersListController;

it('should return form data for a `controller class', function () {
    $action = new RouteAction(
        UsersListController::class,
        '__invoke'
    );

    $vocabulary = Parse::attribute(PostmanFormData::class)
        ->ascend()
        ->in(__DIR__ . '/../../_fixtures/controllers');

    expect($action->getFormData($vocabulary))->toMatchArray(['my' => 'data']);
})->group('route-action', 'route-action::form-data', 'route-action::form-data:controller');

it('should return form data for a `controller method`', function () {
    $action = new RouteAction(
        PostController::class,
        'show'
    );

    $vocabulary = Parse::attribute(PostmanFormData::class)
        ->ascend()
        ->in(__DIR__ . '/../../_fixtures/controllers');

    expect($action->getFormData($vocabulary))->toMatchArray(['my' => 'method data']);
})->group('route-action', 'route-action::form-data', 'route-action::form-data:method');

it('should return form data for a `controller method request`', function () {
    $action = new RouteAction(
        PostController::class,
        'index'
    );

    $vocabulary = Parse::attribute(PostmanFormData::class)
        ->ascend()
        ->in(__DIR__ . '/../../_fixtures/controllers');

    expect($action->getFormData($vocabulary))->toMatchArray(['my' => 'request data']);
})->group('route-action', 'route-action::form-data', 'route-action::form-data:request');

it('should return form data for a `controller method factory`', function () {
    $action = new RouteAction(
        PostController::class,
        'store'
    );

    $vocabulary = Parse::attribute(PostmanFormData::class)
        ->ascend()
        ->in(__DIR__ . '/../../_fixtures/controllers');

    expect($action->getFormData($vocabulary))->toMatchArray(['my' => 'factory data']);
})->group('route-action', 'route-action::form-data', 'route-action::form-data:factory');
