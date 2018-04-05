<?php

namespace Shapecode\Bundle\CronBundle\Command;

use Shapecode\Bundle\CronBundle\Entity\CronJobInterface;
use Shapecode\Bundle\CronBundle\Entity\CronJobResultInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CronProcessCommand
 *
 * @package Shapecode\Bundle\CronBundle\Command
 * @author  Nikita Loges
 */
class CronProcessCommand extends BaseCommand
{

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('shapecode:cron:process');

        $this->addArgument('cron', InputArgument::REQUIRED);
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var CronJobInterface $job */
        $job = $this->getCronJobRepository()->find($input->getArgument('cron'));

        $command = $job->getCommand();
        $watch = 'job-' . $command;

        $output->write("Running " . $job->getCommand() . ": ");

        try {
            $commandToRun = $this->getApplication()->get($job->getCommand());
        } catch (\InvalidArgumentException $ex) {
            $output->writeln(' skipped (command no longer exists)');
            $this->recordJobResult($job, 0, 'Command no longer exists', CronJobResultInterface::SKIPPED);

            // No need to reschedule non-existant commands
            return;
        }

        $emptyInput = new ArrayInput([
            'command' => $job->getCommand()
        ]);
        $jobOutput = new BufferedOutput();

        $this->getStopWatch()->start($watch);

        try {
            $statusCode = $commandToRun->run($emptyInput, $jobOutput);
        } catch (\Exception $ex) {
            // Fail the status code
            $statusCode = 1;
            $jobOutput->writeln('');
            $jobOutput->writeln('Job execution failed with exception ' . get_class($ex) . ':');
        }
        $this->getStopWatch()->stop($watch);

        if (is_null($statusCode)) {
            $statusCode = 0;
        }

        switch ($statusCode) {
            case 0:
                $statusStr = CronJobResultInterface::SUCCEEDED;
                break;
            case 2:
                $statusStr = CronJobResultInterface::SKIPPED;
                break;
            default:
                $statusStr = CronJobResultInterface::FAILED;
        }

        $bufferedOutput = $jobOutput->fetch();
        $output->write($bufferedOutput);

        $duration = $this->getStopWatch()->getEvent($watch)->getDuration();
        $output->writeln($statusStr . ' in ' . number_format(($duration / 1000), 4) . ' seconds');

        // Record the result
        $this->recordJobResult($job, $duration, $bufferedOutput, $statusCode);
    }

    /**
     * @param CronJobInterface $job
     * @param                  $timeTaken
     * @param                  $output
     * @param                  $statusCode
     */
    protected function recordJobResult(CronJobInterface $job, $timeTaken, $output, $statusCode)
    {
        $cronJobRepository = $this->getCronJobRepository();
        $cronJobResultManager = $this->getManager();

        $job = $cronJobRepository->find($job->getId());

        $className = $this->getCronJobResultRepository()->getClassName();

        /** @var CronJobResultInterface $result */
        $result = new $className();
        $result->setCronJob($job);
        $result->setRunTime($timeTaken);
        $result->setOutput($output);
        $result->setStatusCode($statusCode);

        $cronJobResultManager->persist($result);
        $cronJobResultManager->flush();
    }

}
