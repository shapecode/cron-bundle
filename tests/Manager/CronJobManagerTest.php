<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Tests\Manager;

use PHPUnit\Framework\TestCase;
use Shapecode\Bundle\CronBundle\Event\LoadJobsEvent;
use Shapecode\Bundle\CronBundle\Manager\CronJobManager;
use Shapecode\Bundle\CronBundle\Model\CronJobMetadata;
use Symfony\Component\Console\Command\Command;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class CronJobManagerTest extends TestCase
{
    public function testGetApplicationJobs(): void
    {
        $expression  = '* * * * *';
        $commandName = 'value';

        $command = $this->createMock(Command::class);
        $command->method('getName')->willReturn($commandName);

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher
            ->expects(self::exactly(2))
            ->method('dispatch')
            ->willReturnCallback(
                static function (LoadJobsEvent $event) use ($expression, $command): LoadJobsEvent {
                    $event->addJob(
                        CronJobMetadata::createByCommand($expression, $command)
                    );

                    return $event;
                }
            );

        $cronJobManager = new CronJobManager($eventDispatcher);

        $jobs = $cronJobManager->getJobs();

        self::assertCount(1, $jobs);
        self::assertEquals($commandName, $jobs[0]->command);
        self::assertEquals($expression, $jobs[0]->expression);

        // Run second time to assert the same result.
        $jobs = $cronJobManager->getJobs();

        self::assertCount(1, $jobs);
        self::assertEquals($commandName, $jobs[0]->command);
        self::assertEquals($expression, $jobs[0]->expression);
    }
}
