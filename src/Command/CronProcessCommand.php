<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Command;

use RuntimeException;
use Shapecode\Bundle\CronBundle\Console\Style\CronStyle;
use Shapecode\Bundle\CronBundle\Domain\CronJobResultStatus;
use Shapecode\Bundle\CronBundle\Entity\CronJob;
use Shapecode\Bundle\CronBundle\Entity\CronJobResult;
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

final class CronProcessCommand extends BaseCommand
{
    private ?Stopwatch $stopwatch = null;

    protected function configure(): void
    {
        $this->setName('shapecode:cron:process');

        $this->addArgument('cron', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new CronStyle($input, $output);

        $job = $this->getCronJobRepository()->find($input->getArgument('cron'));

        if ($job === null) {
            $io->error('No job found');

            return 1;
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

        $this->getStopWatch()->start($watch);

        if ($job->getRunningInstances() > $job->getMaxInstances()) {
            $statusCode = Command::INVALID;
        } else {
            try {
                $application = $this->getApplication();

                if ($application === null) {
                    throw new RuntimeException('application can not be bull');
                }

                $statusCode = $application->doRun($jobInput, $jobOutput);
            } catch (Throwable $ex) {
                $statusCode = Command::FAILURE;
                $io->error(sprintf('Job execution failed with exception %s: %s', $ex::class, $ex->getMessage()));
            }
        }

        $this->getStopWatch()->stop($watch);

        $status   = CronJobResultStatus::fromCommandStatus($statusCode);
        $duration = $this->getStopWatch()->getEvent($watch)->getDuration();

        $seconds = $duration > 0 ? number_format($duration / 1000, 4) : 0;
        $message = sprintf('%s in %s seconds', $status->getStatusMessage(), $seconds);

        $callback = [
            $io,
            $status->getBlockName(),
        ];
        if (is_callable($callback)) {
            $callback($message);
        }

        // reload job entity - it might be detached from current entity manager by the command
        $job = $this->getCronJobRepository()->find($job->getId());
        if ($job === null) {
            throw new RuntimeException('job not found', 1653421395730);
        }

        // Record the result
        $this->recordJobResult($job, $duration, $jobOutput, $statusCode);

        return $statusCode;
    }

    private function recordJobResult(CronJob $job, float $timeTaken, BufferedOutput $output, int $statusCode): void
    {
        $cronJobResultManager = $this->getManager();

        $buffer = $output->isQuiet() ? null : $output->fetch();

        $result = new CronJobResult(
            $job,
            $timeTaken,
            $statusCode,
            $buffer
        );

        $cronJobResultManager->persist($result);
        $cronJobResultManager->flush();
    }

    private function getStopWatch(): Stopwatch
    {
        if ($this->stopwatch === null) {
            $this->stopwatch = new Stopwatch();
        }

        return $this->stopwatch;
    }
}
