<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Shapecode\Bundle\CronBundle\Entity\CronJob;
use Symfony\Component\Clock\DatePoint;

#[CoversClass(CronJob::class)]
class CronJobTest extends TestCase
{
    public function testCreation(): void
    {
        $job = new CronJob('command', '@daily');
        $job->setLastUse(new DatePoint());
        $job->calculateNextRun();

        self::assertSame('command', $job->getCommand());
    }
}
