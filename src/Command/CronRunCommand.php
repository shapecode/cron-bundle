<?php

namespace Shapecode\Bundle\CronBundle\Command;

use Shapecode\Bundle\CronBundle\Console\Style\CronStyle;
use Shapecode\Bundle\CronBundle\Entity\CronJobInterface;
use Shapecode\Bundle\CronBundle\Entity\CronJobResultInterface;
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

    /** @var string|null */
    protected $projectDir;

    /** @var string|null */
    protected $phpExecutable;

    /** @var string|null */
    protected $consoleBin;

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('shapecode:cron:run');
        $this->setDescription('Runs any currently schedule cron jobs');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $jobRepo = $this->getCronJobRepository();
        $style = new CronStyle($input, $output);

        /** @var CronJobInterface[] $jobsToRun */
        $jobsToRun = $jobRepo->findAll();

        $jobCount = count($jobsToRun);
        $style->comment('Cronjobs started at ' . (new \DateTime())->format('r'));

        $style->title('Execute cronjobs');
        $style->info('Found ' . $jobCount . ' jobs');

        // Update the job with it's next scheduled time
        $now = new \DateTime();

        /** @var Process[] $processes */
        $processes = [];
        $em = $this->getManager();

        foreach ($jobsToRun as $job) {
            sleep(1);

            $style->section('Running "' . $job->getFullCommand() . '"');

            if (!$job->isEnable()) {
                $style->notice('cronjob is disabled');

                continue;
            }

            if ($job->getNextRun() > $now) {
                $style->notice('cronjob will not be executed. Next run is: ' . $job->getNextRun()->format('r'));

                continue;
            }

            $process = $this->runJob($job);

            if ($process) {
                $job->calculateNextRun();
                $job->setLastUse($now);

                $em->persist($job);
                $em->flush();

                $processes[] = $process;
            }

            $style->success('cronjob started successfully and is running in background');
        }

        sleep(1);

        $style->section('Summary');

        if (count($processes)) {
            $style->text('waiting for all running jobs ...');

            // wait for all processes
            $this->waitProcesses($processes);

            $style->success('All jobs are finished.');
        } else {

            $style->info('No jobs were executed. See reasons below.');
        }

        return CronJobResultInterface::EXIT_CODE_SUCCEEDED;
    }

    /**
     * @param Process[] $processes
     */
    public function waitProcesses($processes)
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
     *
     * @return Process|null
     */
    protected function runJob(CronJobInterface $job)
    {
        $consoleBin = $this->getConsoleBin();
        $php = $this->getPhpExecutable();
        $env = $this->getEnvironment();

        $command = sprintf('%s %s shapecode:cron:process %d --env=%s', $php, $consoleBin, $job->getId(), $env);

        $process = new Process($command);
        $process->disableOutput();
        $process->start();

        return $process;
    }

    /**
     * @return string
     */
    protected function getProjectDir()
    {
        if (null !== $this->projectDir) {
            return $this->projectDir;
        }

        $kernel = $this->getKernel();

        // use symfony >3.3 simple way
        if (method_exists($kernel, 'getProjectDir')) {
            $projectDir = $kernel->getProjectDir();
        } else {
            $rootDir = $this->getKernel()->getRootDir();
            $projectDir = dirname($rootDir);
        }

        $this->projectDir = $projectDir;

        return $projectDir;
    }

    /**
     * @return string
     */
    protected function getConsoleBin()
    {
        if (null !== $this->consoleBin) {
            return $this->consoleBin;
        }

        $projectDir = $this->getProjectDir();

        $consolePath = $projectDir . '/bin/console';
        $legacyConsolePath = $projectDir . '/app/console';

        if (file_exists($consolePath)) {
            $consoleBin = $consolePath;
        } elseif (file_exists($legacyConsolePath)) {
            $consoleBin = $legacyConsolePath;
        } else {
            throw new RuntimeException("Missing console binary");
        }

        $this->consoleBin = $consoleBin;

        return $consoleBin;
    }

    /**
     * @return string
     */
    protected function getPhpExecutable()
    {
        if (null !== $this->phpExecutable) {
            return $this->phpExecutable;
        }

        $executableFinder = new PhpExecutableFinder();
        $php = $executableFinder->find();

        if (false === $php) {
            throw new RuntimeException('Unable to find the PHP executable.');
        }

        $this->phpExecutable = $php;

        return $php;
    }

}
