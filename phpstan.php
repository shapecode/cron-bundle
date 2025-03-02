<?php

declare(strict_types=1);

return [
    'includes' => [
        __DIR__ . '/vendor/phpstan/phpstan/conf/bleedingEdge.neon',
        __DIR__ . '/vendor/phpstan/phpstan-strict-rules/rules.neon',
        __DIR__ . '/vendor/phpstan/phpstan-deprecation-rules/rules.neon',
        __DIR__ . '/vendor/phpstan/phpstan-phpunit/extension.neon',
        __DIR__ . '/vendor/phpstan/phpstan-phpunit/rules.neon',
        __DIR__ . '/vendor/phpstan/phpstan-symfony/extension.neon',
        __DIR__ . '/vendor/phpstan/phpstan-symfony/rules.neon',
        __DIR__ . '/vendor/phpstan/phpstan-doctrine/extension.neon',
        __DIR__ . '/vendor/phpstan/phpstan-doctrine/rules.neon',
        __DIR__ . '/phpstan-baseline.php',
    ],
    'parameters' => [
        'level' => 'max',
        'paths' => [
            'src',
            'tests',
        ],
    ],
];
