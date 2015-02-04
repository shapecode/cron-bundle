<?php

namespace Shapecode\Bundle\CronBundle\Command;

use Shapecode\Bundle\CronBundle\Entity\CronJob;
use Shapecode\Bundle\CronBundle\Entity\CronJobResult;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CronRunCommand
 * @package Shapecode\Bundle\CronBundle\Command
 * @author Nikita Loges
 */
class CronRunCommand extends BaseCommand
{

    /** {@inheritdoc} */
    protected $commandName = 'shapecode:cron:run';

    /** {@inheritdoc} */
    protected $commandDescription = 'Runs any currently schedule cron jobs';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this->addArgument('job', InputArgument::OPTIONAL, 'Run only this job (if enabled)');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getStopWatch()->start('cronjobs');

        $jobRepo = $this->getCronJobRepository();

        $jobsToRun = array();
        if ($jobName = $input->getArgument('job')) {
            try {
                $jobObj = $jobRepo->findOneByCommand($jobName);
                if ($jobObj->isEnable()) {
                    $jobsToRun = array($jobObj);
                }
            } catch (\Exception $e) {
                $output->writeln('Couldn\'t find a job by the name of ' . $jobName);

                return CronJobResult::FAILED;
            }
        } else {
            $jobsToRun = $jobRepo->findDueTasks();
        }

        $jobCount = count($jobsToRun);
        $output->writeln("Running $jobCount jobs:");

        foreach ($jobsToRun as $job) {
            $this->runJob($job, $output);
        }

        // Flush our results to the DB
        $this->getEntityManager()->flush();

        $this->getStopWatch()->stop('cronjobs');

        $duration = $this->getStopWatch()->getEvent('cronjobs')->getDuration();

        $output->writeln('Cron run completed in ' . $duration . ' seconds');
    }

    /**
     * @param CronJob $job
     * @param OutputInterface $output
     */
    protected function runJob(CronJob $job, OutputInterface $output)
    {
        $command = $job->getCommand();
        $watch = 'job-' . $command;

        $output->write("Running " . $job->getCommand() . ": ");

        try {
            $commandToRun = $this->getApplication()->get($job->getCommand());
        } catch (\InvalidArgumentException $ex) {
            $output->writeln(' skipped (command no longer exists)');
            $this->recordJobResult($job, 0, 'Command no longer exists', CronJobResult::SKIPPED);

            // No need to reschedule non-existant commands
            return;
        }

        $emptyInput = new ArgvInput();
        $jobOutput = new BufferedOutput();

        $this->getStopWatch()->start($watch);
        try {
            $statusCode = $commandToRun->execute($emptyInput, $jobOutput);
        } catch (\Exception $ex) {
            $statusCode = CronJobResult::FAILED;
            $jobOutput->writeln('');
            $jobOutput->writeln('Job execution failed with exception ' . get_class($ex) . ':');
            $jobOutput->writeln($ex->__toString());
        }
        $this->getStopWatch()->stop($watch);

        if (is_null($statusCode)) {
            $statusCode = 0;
        }

        $statusStr = CronJobResult::FAILED;
        switch ($statusCode) {
            case 0:
                $statusStr = CronJobResult::SUCCEEDED;
                break;
            case 2:
                $statusStr = CronJobResult::SKIPPED;
                break;
        }

        $duration = $this->getStopWatch()->getEvent($watch)->getDuration();
        $output->writeln($statusStr . ' in ' . $duration . ' seconds');

        // Record the result
        $this->recordJobResult($job, $duration, $jobOutput->fetch(), $statusCode);

        // And update the job with it's next scheduled time
        $job->calculateNextRun();
        $job->setLastUse(new \DateTime());
    }

    protected function recordJobResult(CronJob $job, $timeTaken, $output, $statusCode)
    {
        // Create a new CronJobResult
        $result = new CronJobResult();
        $result->setCronJob($job);
        $result->setRunTime($timeTaken);
        $result->setOutput($output);
        $result->setStatusCode($statusCode);

        $this->getEntityManager()->persist($result);
    }

    /**
     * @return \Symfony\Component\Stopwatch\Stopwatch
     */
    protected function getStopWatch()
    {
        return $this->get('debug.stopwatch');
    }
}
