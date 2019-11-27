<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Command;

use RuntimeException;
use Shapecode\Bundle\CronBundle\Console\Style\CronStyle;
use Shapecode\Bundle\CronBundle\Entity\CronJobInterface;
use Shapecode\Bundle\CronBundle\Entity\CronJobResultInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Throwable;
use function get_class;
use function is_callable;
use function number_format;
use function sprintf;
use function str_replace;

final class CronProcessCommand extends BaseCommand
{
    /** @var Stopwatch|null */
    private $stopwatch;

    protected function configure() : void
    {
        $this->setName('shapecode:cron:process');

        $this->addArgument('cron', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $style = new CronStyle($input, $output);

        /** @var CronJobInterface|null $job */
        $job = $this->getCronJobRepository()->find($input->getArgument('cron'));

        if ($job === null) {
            $style->error('No job found');

            return 1;
        }

        $command = $job->getFullCommand() . ' -n';
        $watch   = 'job-' . str_replace(' ', '-', $command);

        $style->title('Running ' . $command);

        $jobInput  = new StringInput($command);
        $jobOutput = new BufferedOutput();

        if ($jobInput->hasParameterOption(['--quiet', '-q'], true) === true) {
            $jobOutput->setVerbosity(OutputInterface::VERBOSITY_QUIET);
        }

        $this->getStopWatch()->start($watch);

        if ($job->getRunningInstances() > $job->getMaxInstances()) {
            $statusCode = CronJobResultInterface::EXIT_CODE_SKIPPED;
        } else {
            try {
                $application = $this->getApplication();

                if ($application === null) {
                    throw new RuntimeException('application can not be bull');
                }

                $statusCode = $application->doRun($jobInput, $jobOutput);
            } catch (Throwable $ex) {
                $statusCode = CronJobResultInterface::EXIT_CODE_FAILED;
                $style->error('Job execution failed with exception ' . get_class($ex) . ': ' . $ex->getMessage());
            }
        }

        $this->getStopWatch()->stop($watch);

        switch ($statusCode) {
            case 0:
                $statusStr = CronJobResultInterface::SUCCEEDED;
                $block     = 'success';
                break;
            case 2:
                $statusStr = CronJobResultInterface::SKIPPED;
                $block     = 'info';
                break;
            default:
                $statusStr = CronJobResultInterface::FAILED;
                $block     = 'error';
        }

        $duration = $this->getStopWatch()->getEvent($watch)->getDuration();

        $seconds = $duration > 0 ? number_format(($duration / 1000), 4) : 0;
        $message = sprintf('%s in %s seconds', $statusStr, $seconds);

        $callback = [$style, $block];
        if (is_callable($callback)) {
            $callback($message);
        }

        // Record the result
        $this->recordJobResult($job, $duration, $jobOutput, $statusCode);

        return $statusCode;
    }

    private function recordJobResult(CronJobInterface $job, float $timeTaken, BufferedOutput $output, int $statusCode) : void
    {
        $cronJobRepository    = $this->getCronJobRepository();
        $cronJobResultManager = $this->getManager();

        /** @var CronJobInterface $job */
        $job = $cronJobRepository->find($job->getId());

        $className = $this->getCronJobResultRepository()->getClassName();

        $buffer = ! $output->isQuiet() ? $output->fetch() : '';

        /** @var CronJobResultInterface $result */
        $result = new $className();
        $result->setCronJob($job);
        $result->setRunTime($timeTaken);
        $result->setOutput($buffer);
        $result->setStatusCode($statusCode);

        $cronJobResultManager->persist($result);
        $cronJobResultManager->flush();
    }

    private function getStopWatch() : Stopwatch
    {
        if ($this->stopwatch === null) {
            $this->stopwatch = new Stopwatch();
        }

        return $this->stopwatch;
    }
}
