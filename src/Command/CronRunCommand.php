<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Command;

use DateTime;
use Doctrine\Common\Persistence\ManagerRegistry;
use Shapecode\Bundle\CronBundle\Console\Style\CronStyle;
use Shapecode\Bundle\CronBundle\Entity\CronJobInterface;
use Shapecode\Bundle\CronBundle\Entity\CronJobResultInterface;
use Shapecode\Bundle\CronBundle\Model\CronJobRunning;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;
use function count;
use function file_exists;
use function sleep;

class CronRunCommand extends BaseCommand
{
    /** @var string|null */
    private $phpExecutable;

    /** @var string|null */
    private $consoleBin;

    /** @var KernelInterface */
    private $kernel;

    public function __construct(
        KernelInterface $kernel,
        ManagerRegistry $registry
    ) {
        parent::__construct($registry);

        $this->kernel = $kernel;
    }

    protected function configure() : void
    {
        $this->setName('shapecode:cron:run');
        $this->setDescription('Runs any currently schedule cron jobs');
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $jobRepo = $this->getCronJobRepository();
        $style   = new CronStyle($input, $output);

        /** @var CronJobInterface[] $jobsToRun */
        $jobsToRun = $jobRepo->findAll();

        $jobCount = count($jobsToRun);
        $style->comment('Cronjobs started at ' . (new DateTime())->format('r'));

        $style->title('Execute cronjobs');
        $style->info('Found ' . $jobCount . ' jobs');

        // Update the job with it's next scheduled time
        $now = new DateTime();

        /** @var CronJobRunning[] $processes */
        $processes = [];
        $em        = $this->getManager();

        foreach ($jobsToRun as $job) {
            sleep(1);

            $style->section('Running "' . $job->getFullCommand() . '"');

            if (! $job->isEnable()) {
                $style->notice('cronjob is disabled');

                continue;
            }

            if ($job->getNextRun() > $now) {
                $style->notice('cronjob will not be executed. Next run is: ' . $job->getNextRun()->format('r'));

                continue;
            }

            $job->increaseRunningInstances();
            $process = $this->runJob($job);

            $job->calculateNextRun();
            $job->setLastUse($now);

            $em->persist($job);
            $em->flush();

            $processes[] = new CronJobRunning($job, $process);

            if ($job->getRunningInstances() > $job->getMaxInstances()) {
                $style->notice('cronjob will not be executed. The number of maximum instances has been exceeded.');
            } else {
                $style->success('cronjob started successfully and is running in background');
            }
        }

        sleep(1);

        $style->section('Summary');

        if (count($processes) > 0) {
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
     * @param CronJobRunning[] $processes
     */
    public function waitProcesses(array $processes) : void
    {
        $em = $this->getManager();

        while (count($processes) > 0) {
            foreach ($processes as $key => $running) {
                $process = $running->getProcess();

                if ($process->isRunning() === true) {
                    break;
                }

                $job = $running->getCronJob();
                $job->decreaseRunningInstances();

                $em->persist($job);
                $em->flush();

                unset($processes[$key]);
            }
        }
    }

    private function runJob(CronJobInterface $job) : Process
    {
        $command = [
            $this->getPhpExecutable(),
            $this->getConsoleBin(),
            'shapecode:cron:process',
            $job->getId(),
        ];

        $process = new Process($command);
        $process->disableOutput();
        $process->start();

        return $process;
    }

    private function getConsoleBin() : string
    {
        if ($this->consoleBin !== null) {
            return $this->consoleBin;
        }

        $projectDir = $this->kernel->getProjectDir();

        $consolePath = $projectDir . '/bin/console';

        if (! file_exists($consolePath)) {
            throw new RuntimeException('Missing console binary');
        }

        $consoleBin = $consolePath;

        $this->consoleBin = $consoleBin;

        return $consoleBin;
    }

    private function getPhpExecutable() : string
    {
        if ($this->phpExecutable !== null) {
            return $this->phpExecutable;
        }

        $executableFinder = new PhpExecutableFinder();
        $php              = $executableFinder->find();

        if ($php === false) {
            throw new RuntimeException('Unable to find the PHP executable.');
        }

        $this->phpExecutable = $php;

        return $php;
    }
}
