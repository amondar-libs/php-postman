<?php

declare(strict_types = 1);

use function Spatie\PestPluginTestTime\testTime;

beforeEach(fn() => testTime()->freeze('2026-01-06 20:00:00'));

it('should export `All Laravel` routes', function () {
    $data = Amondar\Postman\Export::from($this->routes, 'http://localhost/')
        ->parseDataIn(__DIR__ . '/../../_fixtures/controllers')
        ->useOAuthTokenPath('/oauth/token')
        ->toArray();

    expect($data)->toMatchArray(readJsonFixture('laravel_export_result.json'));
})->group('laravel', 'export');
