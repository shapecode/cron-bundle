<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Tests\Service;

use PHPUnit\Framework\TestCase;
use Shapecode\Bundle\CronBundle\Service\CommandHelper;
use Symfony\Component\HttpKernel\Kernel;

use function realpath;
use function sprintf;

use const PHP_BINARY;

class CommandHelperTest extends TestCase
{
    public function testGetConsoleBin(): void
    {
        $path = realpath(__DIR__ . '/../Fixtures');

        $kernel = $this->createMock(Kernel::class);
        $kernel->method('getProjectDir')->willReturn($path);

        $helper = new CommandHelper($kernel);

        self::assertEquals(
            sprintf('%s/bin/console', $path),
            $helper->getConsoleBin()
        );
    }

    public function testGetPhpExecutable(): void
    {
        $kernel = $this->createMock(Kernel::class);
        $kernel->method('getProjectDir')->willReturn(__DIR__);

        $helper = new CommandHelper($kernel);

        self::assertEquals(
            PHP_BINARY,
            $helper->getPhpExecutable()
        );
    }
}
