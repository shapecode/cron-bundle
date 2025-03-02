<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Tests\Entity;

use DateTimeZone;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Shapecode\Bundle\CronBundle\Entity\CronJob;
use Shapecode\Bundle\CronBundle\Entity\CronJobResult;
use Symfony\Component\Clock\DatePoint;

#[CoversClass(CronJobResult::class)]
class CronJobResultTest extends TestCase
{
    public function testCreation(): void
    {
        $result = new CronJobResult(
            new CronJob('command', '@daily'),
            0.0,
            0,
            null,
            new DatePoint('2025-03-02 19:06:00', new DateTimeZone('UTC')),
        );

        self::assertSame('command - 02.03.2025 19:06 +00:00', $result->__toString());
    }
}
