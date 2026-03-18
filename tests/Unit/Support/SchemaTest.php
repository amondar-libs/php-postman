<?php

declare(strict_types = 1);

use Amondar\Postman\Support\Schema;

it('should work properly with: `variables`', function () {
    $data = Schema::for('test')->addVariable('my', 'value')->toArray();

    expect($data['variable'])->toMatchArray([
        [
            'key'   => 'my',
            'value' => 'value',
        ],
    ]);
})->group('schema', 'schema::variables');

it('should work properly with: `base-info`', function () {
    $data = Schema::for('test', 'custom description')->toArray();

    expect($data['info'])->toMatchArray([
        'name'        => 'test',
        'description' => 'custom description',
        'schema'      => 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json',
    ]);
})->group('schema', 'schema::base-info');

it('should work properly with: `pushRequest`', function () {
    $nameStructure = ['my', 'base', 'structure'];
    $data = Schema::for('test')->pushRequest($nameStructure, makeFakeRequest('my_request'))->toArray();

    $creature = [
        'name' => 'null',
        'item' => $data['item'],
    ];

    foreach (['null', ...$nameStructure] as $segment) {
        expect($creature['name'])->toBe($segment)
            ->and($creature['item'])->toHaveCount(1);

        $creature = $creature['item'][0];
    }

    expect($creature['name'])->toBe('my_request')
        ->and($creature['request'])->toMatchArray(makeFakeRequest('my_request')->data->toArray());
})->group('schema', 'schema::push-request');
