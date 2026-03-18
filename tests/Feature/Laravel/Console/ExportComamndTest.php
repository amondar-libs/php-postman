<?php

declare(strict_types = 1);

use Symfony\Component\Console\Command\Command;

use function Spatie\PestPluginTestTime\testTime;

beforeEach(fn() => testTime()->freeze('2026-01-06 20:00:00'));

$filePath = __DIR__ . '/../../../_fixtures/postman.json';

it('should:  `export as plain json`', function () {
    $tester = makeExportCommandTester();

    $exitCode = $tester->execute([
        '--laravel'          => true,
        '--format'           => 'json',
        '-name'              => 'postman',
        '-description'       => 'postman description',
        '-attributes'        => [__DIR__ . '/../../../_fixtures/controllers'],
        '-url'               => 'http://localhost',
        '-oauth-url'         => '/something-before/oauth/token',
    ]);

    expect($exitCode)->toBe(Command::SUCCESS)
        ->and($tester->getDisplay())->toBeJson()->toEqual(readJsonFixture('export_command_result.json', false));
})
    ->group('laravel', 'laravel::console:export')
    ->after(fn() => is_file($filePath) ? unlink($filePath) : null);

it('should:  `export as a file`', function () {
    $tester = makeExportCommandTester();

    $exitCode = $tester->execute([
        '--laravel'          => true,
        '--format'           => 'json',
        '--output'           => __DIR__ . '/../../../_fixtures/postman.json',
        '-name'              => 'postman',
        '-description'       => 'postman description',
        '-attributes'        => [__DIR__ . '/../../../_fixtures/controllers'],
        '-url'               => 'http://localhost',
        '-oauth-url'         => '/something-before/oauth/token',
    ]);

    expect($exitCode)->toBe(Command::SUCCESS)
        ->and(readJsonFixture('postman.json'))->toMatchArray(readJsonFixture('export_command_result.json'));
})
    ->group('laravel', 'laravel::console:export')
    ->after(fn() => is_file($filePath) ? unlink($filePath) : null);

it('should:  `filter routes by path`', function () {
    $tester = makeExportCommandTester();

    $exitCode = $tester->execute([
        '--laravel'          => true,
        '--format'           => 'json',
        '-name'              => 'postman',
        '-description'       => 'postman description',
        '-url'               => 'http://localhost',
        '-oauth-url'         => '/something-before/oauth/token',
        '-paths'             => ['*/v1/users;*/v2/users'],
    ]);

    expect($exitCode)->toBe(Command::SUCCESS)
        ->and($tester->getDisplay())->toBeJson()->toContain('"path":["api","v1","users"]', '"path":["api","v2","users"]')
        ->not->toContain('"path":["api","v1","posts"]', '"path":["home"]');
})
    ->group('laravel', 'laravel::console:export')
    ->after(fn() => is_file($filePath) ? unlink($filePath) : null);

it('should:  `filter routes by name`', function () {
    $tester = makeExportCommandTester();

    $exitCode = $tester->execute([
        '--laravel'          => true,
        '--format'           => 'json',
        '-name'              => 'postman',
        '-description'       => 'postman description',
        '-url'               => 'http://localhost',
        '-oauth-url'         => '/something-before/oauth/token',
        '-names'             => ['api.v2*'],
    ]);

    expect($exitCode)->toBe(Command::SUCCESS)
        ->and($tester->getDisplay())->toBeJson()->toContain('"path":["api","v2","users"]')
        ->not->toContain('"path":["api","v1","users"]', '"path":["api","v1","posts"]', '"path":["home"]');
})
    ->group('laravel', 'laravel::console:export')
    ->after(fn() => is_file($filePath) ? unlink($filePath) : null);

it('should:  `filter routes by methods`', function () {
    $tester = makeExportCommandTester();

    $exitCode = $tester->execute([
        '--laravel'            => true,
        '--format'             => 'json',
        '-name'                => 'postman',
        '-description'         => 'postman description',
        '-url'                 => 'http://localhost',
        '-oauth-url'           => '/something-before/oauth/token',
        '-methods'             => ['delete;patch'],
    ]);

    expect($exitCode)->toBe(Command::SUCCESS)
        ->and($tester->getDisplay())->toBeJson()->toContain('"path":["api","v2","posts","{post}"]')
        ->not->toContain('"path":["api","v1","users"]', '"path":["api","v1","posts"]', '"path":["home"]');
})
    ->group('laravel', 'laravel::console:export')
    ->after(fn() => is_file($filePath) ? unlink($filePath) : null);

it('should:  `filter routes by middlewares`', function () {
    $tester = makeExportCommandTester();

    $exitCode = $tester->execute([
        '--laravel'                => true,
        '--format'                 => 'json',
        '-name'                    => 'postman',
        '-description'             => 'postman description',
        '-url'                     => 'http://localhost',
        '-oauth-url'               => '/something-before/oauth/token',
        '-middlewares'             => ['auth:*'],
    ]);

    expect($exitCode)->toBe(Command::SUCCESS)
        ->and($tester->getDisplay())->toBeJson()->toContain('"path":["api","v2","posts","{post}"]', '"path":["api","v2","posts"]', '"path":["api","v2","users"]', '"path":["api","v1","users"]')
        ->not->toContain('"path":["home"]');
})
    ->group('laravel', 'laravel::console:export')
    ->after(fn() => is_file($filePath) ? unlink($filePath) : null);
