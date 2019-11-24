<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Tests\Manager;

use Shapecode\Bundle\CronBundle\Event\LoadJobsEvent;
use Shapecode\Bundle\CronBundle\Manager\CronJobManager;
use Shapecode\Bundle\CronBundle\Model\CronJobMetadata;
use Shapecode\Bundle\CronBundle\Tests\TestCase;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class CronJobManagerTest extends TestCase
{
    public function testGetApplicationJobs() : void
    {
        $expression = '* * * * *';
        $command    = 'value';

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher
            ->expects(self::once())
            ->method('dispatch')
            ->willReturnCallback(
                static function (LoadJobsEvent $event) use ($expression, $command) : void {
                    $event->addJob(
                        new CronJobMetadata($expression, $command)
                    );
                }
            );

        $cronJobManager = new CronJobManager($eventDispatcher);

        $jobs = $cronJobManager->getJobs();

        $this->assertCount(1, $jobs);

        $this->assertInstanceOf(CronJobMetadata::class, $jobs[0]);
        $this->assertEquals($command, $jobs[0]->getCommand());
        $this->assertEquals($expression, $jobs[0]->getExpression());

        // Run second time to assert the same result.
        $jobs = $cronJobManager->getJobs();
        $this->assertCount(1, $jobs);
        $this->assertInstanceOf(CronJobMetadata::class, $jobs[0]);
        $this->assertEquals($command, $jobs[0]->getCommand());
        $this->assertEquals($expression, $jobs[0]->getExpression());
    }
}
