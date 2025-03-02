<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Tests\Command;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shapecode\Bundle\CronBundle\Command\CronRunCommand;
use Shapecode\Bundle\CronBundle\CronJob\CommandHelper;
use Shapecode\Bundle\CronBundle\Entity\CronJob;
use Shapecode\Bundle\CronBundle\Repository\CronJobRepository;
use Symfony\Component\Clock\MockClock;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpKernel\Kernel;

final class CronRunCommandTest extends TestCase
{
    private Kernel & MockObject $kernel;

    private CommandHelper & MockObject $commandHelper;

    private EntityManagerInterface & MockObject $manager;

    private CronJobRepository & MockObject $cronJobRepo;

    private CronRunCommand $command;

    private InputInterface & MockObject $input;

    private BufferedOutput $output;

    private MockClock $clock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->kernel        = $this->createMock(Kernel::class);
        $this->manager       = $this->createMock(EntityManagerInterface::class);
        $this->commandHelper = $this->createMock(CommandHelper::class);
        $this->cronJobRepo   = $this->createMock(CronJobRepository::class);
        $this->input         = $this->createMock(InputInterface::class);
        $this->output        = new BufferedOutput();

        $this->clock = new MockClock();

        $this->command = new CronRunCommand(
            $this->manager,
            $this->cronJobRepo,
            $this->commandHelper,
            $this->clock,
        );
    }

    public function testRun(): void
    {
        $this->kernel->method('getProjectDir')->willReturn(__DIR__);

        $this->commandHelper->method('getConsoleBin')->willReturn('/bin/console');
        $this->commandHelper->method('getPhpExecutable')->willReturn('php');
        $this->commandHelper->method('getTimeout')->willReturn(null);

        $job = CronJob::create('pwd', '* * * * *');
        $job->setNextRun(new DateTime());

        $this->cronJobRepo->method('findAll')->willReturn([
            $job,
        ]);

        $this->command->run($this->input, $this->output);

        self::assertSame('shapecode:cron:run', $this->command->getName());
    }

    public function testRunWithTimeout(): void
    {
        $this->kernel->method('getProjectDir')->willReturn(__DIR__);

        $this->commandHelper->method('getConsoleBin')->willReturn('/bin/console');
        $this->commandHelper->method('getPhpExecutable')->willReturn('php');
        $this->commandHelper->method('getTimeout')->willReturn(30.0);

        $this->manager = $this->createMock(EntityManagerInterface::class);

        $job = CronJob::create('pwd', '* * * * *');
        $job->setNextRun(new DateTime());

        $this->cronJobRepo->method('findAll')->willReturn([
            $job,
        ]);

        $this->command->run($this->input, $this->output);

        self::assertSame('shapecode:cron:run', $this->command->getName());
    }
}
