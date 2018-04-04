<?php

namespace Shapecode\Bundle\CronBundle\Command;

use Shapecode\Bundle\CronBundle\Entity\CronJobInterface;
use Shapecode\Bundle\CronBundle\Entity\CronJobResultInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\PhpExecutableFinder;
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
                $output->writeln('Couldn\'t find a job by the name of '.$jobName);

                return CronJobResultInterface::FAILED;
            }
        } else {
            $jobsToRun = $jobRepo->findDueTasks();
        }

        $jobCount = count($jobsToRun);
        $output->writeln('Cronjobs started at '.(new \DateTime())->format('r'));
        $output->writeln('Running '.$jobCount.' jobs');

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
        $this->waitProcesses($processes);
    }

    /**
     * @param Process[] $processes
     */
    public function waitProcesses($processes): void
    {
        $wait = true;
        while ($wait) {
            $wait = false;

            foreach ($processes as $process) {
                if ($process->isRunning()) {
                    $wait = true;
                    break;
                }
            }
        }
    }

    /**
     * @param CronJobInterface $job
     * @param OutputInterface $output
     *
     * @return Process|null
     */
    protected function runJob(CronJobInterface $job, OutputInterface $output)
    {
        $output->writeln("Running ".$job->getCommand());

        $rootDir = $this->getKernel()->getRootDir();
        $projectDir = realpath($rootDir.'/..');

        $consolePath = $projectDir.'/bin/console';
        $legacyConsolePath = $projectDir.'/app/console';

        if (file_exists($consolePath)) {
            $consoleBin = $consolePath;
        } elseif (file_exists($legacyConsolePath)) {
            $consoleBin = $legacyConsolePath;
        } else {
            throw new RuntimeException("Missing console binary");
        }

        $executableFinder = new PhpExecutableFinder();
        $php = $executableFinder->find();

        if (false === $php) {
            throw new RuntimeException('Unable to find the PHP executable.');
        }

        $command = sprintf('%s %s shapecode:cron:process %d', $php, $consoleBin, $job->getId());

        $process = new Process($command);
        $process->disableOutput();
        $process->start();

        return $process;
    }


}
