<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Tests\Command;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Shapecode\Bundle\CronBundle\Collection\CronJobCollection;
use Shapecode\Bundle\CronBundle\Command\CronJobDisableCommand;
use Shapecode\Bundle\CronBundle\Entity\CronJob;
use Shapecode\Bundle\CronBundle\Repository\CronJobRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CronJobDisableCommandTest extends TestCase
{
    public function testExecuteJobNotFound(): void
    {
        $entityManager     = self::createStub(EntityManagerInterface::class);
        $cronJobRepository = self::createStub(CronJobRepository::class);
        $cronJobRepository->method('findByCommandOrId')
            ->willReturn(new CronJobCollection());

        $command = new CronJobDisableCommand($entityManager, $cronJobRepository);

        $input  = self::createStub(InputInterface::class);
        $output = self::createStub(OutputInterface::class);

        $input->method('getArgument')
            ->willReturn('non-existing-job');

        $result = $command->run($input, $output);

        self::assertSame(Command::FAILURE, $result);
    }

    public function testExecuteJobDisabledSuccessfully(): void
    {
        $cronJob = $this->createMock(CronJob::class);
        $cronJob->expects($this->once())
            ->method('disable');

        $cronJobCollection = new CronJobCollection($cronJob);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
            ->method('persist')
            ->with($cronJob);
        $entityManager->expects($this->once())
            ->method('flush');

        $cronJobRepository = self::createStub(CronJobRepository::class);
        $cronJobRepository->method('findByCommandOrId')
            ->willReturn($cronJobCollection);

        $command = new CronJobDisableCommand($entityManager, $cronJobRepository);

        $input  = self::createStub(InputInterface::class);
        $output = self::createStub(OutputInterface::class);

        $input->method('getArgument')
            ->willReturn('existing-job');

        $result = $command->run($input, $output);

        self::assertSame(Command::SUCCESS, $result);
    }
}
