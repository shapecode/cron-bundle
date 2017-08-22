<?php

namespace Shapecode\Bundle\CronBundle\Command;

use Shapecode\Bundle\CronBundle\Entity\Interfaces\CronJobInterface;
use Shapecode\Bundle\CronBundle\Entity\Interfaces\CronJobResultInterface;
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
        parent::configure();

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
        $output->writeln('Running ' . $jobCount . ' jobs:');

        // Update the job with it's next scheduled time
        $now = new \DateTime();
        foreach ($jobsToRun as $job) {
            $job->calculateNextRun();
            $job->setLastUse($now);

            $this->getEntityManager()->persist($job);
        }

        // flush the calculated runs
        $this->getEntityManager()->flush();

        // Run the jobs
        foreach ($jobsToRun as $job) {
            $this->runJob($job, $output);
        }
    }

    /**
     * @param CronJobInterface $job
     * @param OutputInterface  $output
     *
     * @return string
     */
    protected function runJob(CronJobInterface $job, OutputInterface $output)
    {
        $output->writeln("Running " . $job->getCommand());

        try {
            $process = new Process('php app/console shapecode:cron:process ' . $job->getId());
            $process->start();
        } catch (\Exception $e) {
        }

        try {
            $process = new Process('php bin/console shapecode:cron:process ' . $job->getId());
            $process->start();
        } catch (\Exception $e) {
        }
    }

}
