<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Tests\Command;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;
use Shapecode\Bundle\CronBundle\Command\CronRunCommand;
use Shapecode\Bundle\CronBundle\Entity\CronJobInterface;
use Shapecode\Bundle\CronBundle\Model\CronJobRunning;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\Process;

class CronRunCommandTest extends TestCase
{
    public function testWaitProcesses() : void
    {
        $kernel   = $this->createMock(KernelInterface::class);
        $manager  = $this->createMock(ObjectManager::class);
        $registry = $this->createMock(ManagerRegistry::class);
        $registry->method('getManager')->willReturn($manager);

        $command = $this->createTestProxy(CronRunCommand::class, [
            $kernel,
            $registry,
        ]);

        $cronJob = $this->createMock(CronJobInterface::class);
        $process = $this->createMock(Process::class);
        $process
            ->expects(self::exactly(3))
            ->method('isRunning')
            ->willReturnOnConsecutiveCalls(true, true, false);

        $processes[] = new CronJobRunning(
            $cronJob,
            $process
        );

        $command->waitProcesses($processes);
    }
}
