<?php

declare(strict_types = 1);

namespace Tests\Unit\Route;

use Amondar\Postman\Route\RouteFilter;

it('should filter `byName`', function () {
    $filter = RouteFilter::apply()->byName(['home', 'h.*.m.*']);

    expect($filter->test(makeFakeRoute(name: 'not.a.ho.me')))
        ->toBeFalse()
        ->and($filter->test(makeFakeRoute(name: 'h.o.m.e')))
        ->toBeTrue()
        ->and($filter->test(makeFakeRoute(name: 'home')))
        ->toBeTrue();

})->group('route-filter', 'route-filter::byName');

it('should filter `byPath`', function () {
    $filter = RouteFilter::apply()->byPath(['api/v1*', 'api/v2*']);

    expect($filter->test(makeFakeRoute(path: 'api/v3/closure')))
        ->toBeFalse()
        ->and($filter->test(makeFakeRoute(path: 'api/v1/closure')))
        ->toBeTrue()
        ->and($filter->test(makeFakeRoute(path: 'api/v2/closure')))
        ->toBeTrue();
})->group('route-filter', 'route-filter::byPath');

it('should filter `byMethod`', function () {
    $filter = RouteFilter::apply()->byMethod(['GET', 'POST']);

    expect($filter->test(makeFakeRoute(methods: ['PATCH', 'PUT'])))
        ->toBeFalse()
        ->and($filter->test(makeFakeRoute(methods: ['POST'])))
        ->toBeTrue()
        ->and($filter->test(makeFakeRoute(methods: ['GET'])))
        ->toBeTrue();
})->group('route-filter', 'route-filter::byMethod');

it('should filter `byMiddleware`', function () {
    $filter = RouteFilter::apply()->byMiddleware(['api', 'auth*']);

    expect($filter->test(makeFakeRoute(middleware: ['web'])))
        ->toBeFalse()
        ->and($filter->test(makeFakeRoute(middleware: ['api'])))
        ->toBeTrue()
        ->and($filter->test(makeFakeRoute(middleware: ['auth:sanctum', 'auth:api'])))
        ->toBeTrue();
})->group('route-filter', 'route-filter::byMiddleware');
