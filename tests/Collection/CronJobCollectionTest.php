<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Tests\Collection;

use PHPUnit\Framework\TestCase;
use Shapecode\Bundle\CronBundle\Collection\CronJobCollection;
use Shapecode\Bundle\CronBundle\Entity\CronJob;

class CronJobCollectionTest extends TestCase
{
    public function testMapToCommandWithMultipleCronJobs(): void
    {
        $cronJob1 = self::createStub(CronJob::class);
        $cronJob1->method('getCommand')->willReturn('command:one');

        $cronJob2 = self::createStub(CronJob::class);
        $cronJob2->method('getCommand')->willReturn('command:two');

        $cronCollection = new CronJobCollection($cronJob1, $cronJob2);

        $result = $cronCollection->mapToCommand();

        self::assertCount(2, $result);
        self::assertSame(['command:one', 'command:two'], $result->toArray());
    }

    public function testMapToCommandWithSingleCronJob(): void
    {
        $cronJob = self::createStub(CronJob::class);
        $cronJob->method('getCommand')->willReturn('command:single');

        $cronCollection = new CronJobCollection($cronJob);

        $result = $cronCollection->mapToCommand();

        self::assertCount(1, $result);
        self::assertSame(['command:single'], $result->toArray());
    }

    public function testMapToCommandWithEmptyCollection(): void
    {
        $cronCollection = new CronJobCollection();

        $result = $cronCollection->mapToCommand();

        self::assertCount(0, $result);
        self::assertSame([], $result->toArray());
    }
}
