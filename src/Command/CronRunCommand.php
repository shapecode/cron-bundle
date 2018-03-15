<?php

namespace Shapecode\Bundle\CronBundle\Command;

use Shapecode\Bundle\CronBundle\Entity\CronJobInterface;
use Shapecode\Bundle\CronBundle\Entity\CronJobResultInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * Class CronRunCommand
 *
 * @package Shapecode\Bundle\CronBundle\Command
 * @author  Nikita Loges
 */
class CronRunCommand extends BaseCommand
{

    /** @inheritdoc */
    protected $commandName = 'shapecode:cron:run';

    /** @inheritdoc */
    protected $commandDescription = 'Runs any currently schedule cron jobs';

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('shapecode:cron:run');
        $this->setDescription('Runs any currently schedule cron jobs');

        $this->addArgument('job', InputArgument::OPTIONAL, 'Run only this job (if enabled)');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $jobRepo = $this->getCronJobRepository();

        $jobsToRun = [];
        if ($jobName = $input->getArgument('job')) {
            try {
                $jobObj = $jobRepo->findOneByCommand($jobName);
                if ($jobObj->isEnable()) {
                    $jobsToRun = [$jobObj];
                }
            } catch (\Exception $e) {
                $output->writeln('Couldn\'t find a job by the name of ' . $jobName);

                return CronJobResultInterface::FAILED;
            }
        } else {
            $jobsToRun = $jobRepo->findDueTasks();
        }

        $jobCount = count($jobsToRun);
        $output->writeln('Cronjobs started at ' . (new \DateTime())->format('r'));
        $output->writeln('Running ' . $jobCount . ' jobs');

        // Update the job with it's next scheduled time
        $now = new \DateTime();
        foreach ($jobsToRun as $job) {
            $job->calculateNextRun();
            $job->setLastUse($now);

            $this->getManager()->persist($job);
        }

        // flush the calculated runs
        $this->getManager()->flush();

        /** @var Process[] $processes */
        $processes = [];

        // Run the jobs
        foreach ($jobsToRun as $job) {
            $process = $this->runJob($job, $output);

            if ($process) {
                $processes[] = $process;
            }
        }

        // wait for all processes
        $wait = true;
        while ($wait) {
            $wait = false;

            foreach ($processes as $process) {
                if ($process->isRunning()) {
                    $wait = true;
                }
            }
        }
    }

    /**
     * @param CronJobInterface $job
     * @param OutputInterface  $output
     *
     * @return Process|null
     */
    protected function runJob(CronJobInterface $job, OutputInterface $output)
    {
        $output->writeln("Running " . $job->getCommand());

        $rootDir = $this->kernel->getRootDir();
        $projectDir = $rootDir . '/..';

        $binaryDir = $projectDir . '/bin';
        $legacyBinaryDir = $projectDir . '/app';

        $command = null;

        if (file_exists($legacyBinaryDir . '/console')) {
            $command = 'php app/console shapecode:cron:process ' . $job->getId();
        }

        if (file_exists($binaryDir . '/console')) {
            $command = 'php bin/console shapecode:cron:process ' . $job->getId();
        }

        if ($command) {
            try {
                $process = new Process($command);
                $process->disableOutput();
                $process->start();

                return $process;
            } catch (\Exception $e) {

            }
        }

        return null;
    }

}
