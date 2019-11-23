<?php

declare(strict_types=1);

$file = __DIR__ . '/../vendor/autoload.php';
if (! file_exists($file)) {
    throw new RuntimeException('Install composer dependencies to run test suite.');
}
$autoload = require $file;
