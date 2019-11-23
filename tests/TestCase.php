<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Tests;

use Mockery;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TestCase extends KernelTestCase
{
    protected function tearDown() : void
    {
        Mockery::close();
    }
}
