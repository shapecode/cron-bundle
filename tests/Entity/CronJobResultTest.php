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

    public function testCronJobSuccessStatus(): void
    {
        $cronJob = new CronJob('command', '@hourly');
        $result  = new CronJobResult(
            $cronJob,
            1.23,
            0,
            null,
            new DatePoint('2025-03-03 12:00:00', new DateTimeZone('UTC')),
        );

        self::assertSame(0, $result->getStatusCode());
    }

    public function testCronJobFailureStatus(): void
    {
        $cronJob = new CronJob('command', '@hourly');
        $result  = new CronJobResult(
            $cronJob,
            3.45,
            1,
            'Error occurred',
            new DatePoint('2025-03-03 12:15:00', new DateTimeZone('UTC')),
        );

        self::assertSame(1, $result->getStatusCode());
        self::assertSame('Error occurred', $result->getOutput());
    }

    public function testCronJobExecutionTime(): void
    {
        $cronJob = new CronJob('command', '@daily');
        $result  = new CronJobResult(
            $cronJob,
            2.56,
            0,
            null,
            new DatePoint('2025-03-04 08:00:00', new DateTimeZone('UTC')),
        );

        self::assertSame(2.56, $result->getRunTime());
    }

    public function testCronJobResultDetails(): void
    {
        $cronJob = new CronJob('test:command', '@weekly');
        $result  = new CronJobResult(
            $cronJob,
            5.67,
            0,
            'Execution successful',
            new DatePoint('2025-03-05 10:30:00', new DateTimeZone('UTC')),
        );

        self::assertSame('test:command', $result->getCronJob()->getCommand());
        self::assertSame('Execution successful', $result->getOutput());
        self::assertSame('test:command - 05.03.2025 10:30 +00:00', $result->__toString());
    }
}
