<?php

declare(strict_types=1);

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;

return new Configuration()
    // Adjusting scanned paths
    ->addPathToScan(__DIR__ . '/src', false)
    ->addPathToScan(__DIR__ . '/tests', true);
