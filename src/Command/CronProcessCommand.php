<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Clock\ClockInterface;
use RuntimeException;
use Shapecode\Bundle\CronBundle\Console\Style\CronStyle;
use Shapecode\Bundle\CronBundle\Domain\CronJobResultStatus;
use Shapecode\Bundle\CronBundle\Entity\CronJob;
use Shapecode\Bundle\CronBundle\Entity\CronJobResult;
use Shapecode\Bundle\CronBundle\Repository\CronJobRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Throwable;

use function is_callable;
use function number_format;
use function sprintf;
use function str_replace;

#[AsCommand(
    name: CronProcessCommand::NAME,
)]
final class CronProcessCommand extends Command
{
    public const string NAME = 'shapecode:cron:process';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CronJobRepository $cronJobRepository,
        private readonly Stopwatch $stopwatch,
        private readonly ClockInterface $clock,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('cron', InputArgument::REQUIRED);
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int {
        $io = new CronStyle($input, $output);

        $job = $this->cronJobRepository->find($input->getArgument('cron'));

        if ($job === null) {
            $io->error('No job found');

            return Command::SUCCESS;
        }

        $command = sprintf('%s -n', $job->getFullCommand());
        $watch   = sprintf('job-%s', str_replace(' ', '-', $command));

        $io->title(sprintf('Running %s', $command));

        $jobInput  = new StringInput($command);
        $jobOutput = new BufferedOutput();

        if (
            $jobInput->hasParameterOption([
                '--quiet',
                '-q',
            ], true) === true
        ) {
            $jobOutput->setVerbosity(OutputInterface::VERBOSITY_QUIET);
        }

        $this->stopwatch->start($watch);

        if ($job->getRunningInstances() > $job->getMaxInstances()) {
            $statusCode = Command::INVALID;
        } else {
            try {
                $application = $this->getApplication();

                if ($application === null) {
                    throw new RuntimeException('application can not be bull', 1653426731910);
                }

                $statusCode = $application->doRun($jobInput, $jobOutput);
            } catch (Throwable $ex) {
                $statusCode = Command::FAILURE;
                $io->error(sprintf('Job execution failed with exception %s: %s', $ex::class, $ex->getMessage()));
            }
        }

        $this->stopwatch->stop($watch);

        $status   = CronJobResultStatus::fromCommandStatus($statusCode);
        $duration = $this->stopwatch->getEvent($watch)->getDuration();

        $seconds = $duration > 0 ? number_format($duration / 1000, 4) : 0;
        $message = sprintf('%s in %s seconds', $status->getStatusMessage(), $seconds);

        $callback = [$io, $status->getBlockName()];
        if (is_callable($callback)) {
            $callback($message);
        }

        // reload job entity - it might be detached from current entity manager by the command
        $job = $this->cronJobRepository->find($job->getId());
        if ($job === null) {
            throw new RuntimeException('job not found', 1653421395730);
        }

        // Record the result
        $this->recordJobResult($job, $duration, $jobOutput, $statusCode);

        return $statusCode;
    }

    private function recordJobResult(
        CronJob $job,
        float $timeTaken,
        BufferedOutput $output,
        int $statusCode,
    ): void {
        $buffer = $output->isQuiet() ? null : $output->fetch();

        $result = new CronJobResult(
            $job,
            $timeTaken,
            $statusCode,
            $buffer,
            $this->clock->now(),
        );

        $this->entityManager->persist($result);
        $this->entityManager->flush();
    }
}
