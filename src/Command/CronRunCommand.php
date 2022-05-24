<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Command;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Shapecode\Bundle\CronBundle\Console\Style\CronStyle;
use Shapecode\Bundle\CronBundle\Entity\CronJob;
use Shapecode\Bundle\CronBundle\Model\CronJobRunning;
use Shapecode\Bundle\CronBundle\Repository\CronJobRepository;
use Shapecode\Bundle\CronBundle\Service\CommandHelper;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Process;

use function count;
use function sleep;
use function sprintf;

#[AsCommand(
    name: 'shapecode:cron:run',
    description: 'Runs any currently schedule cron jobs'
)]
final class CronRunCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CronJobRepository $cronJobRepository,
        private readonly CommandHelper $commandHelper,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new CronStyle($input, $output);

        $jobsToRun = $this->cronJobRepository->findAll();

        $jobCount = count($jobsToRun);
        $style->comment(sprintf('Cronjobs started at %s', (new DateTime())->format('r')));

        $style->title('Execute cronjobs');
        $style->info(sprintf('Found %d jobs', $jobCount));

        // Update the job with it's next scheduled time
        $now = new DateTime();

        /** @var CronJobRunning[] $processes */
        $processes = [];

        foreach ($jobsToRun as $job) {
            sleep(1);

            $style->section(sprintf('Running "%s"', $job->getFullCommand()));

            if (! $job->isEnable()) {
                $style->notice('cronjob is disabled');

                continue;
            }

            if ($job->getNextRun() > $now) {
                $style->notice(sprintf('cronjob will not be executed. Next run is: %s', $job->getNextRun()->format('r')));

                continue;
            }

            $job->increaseRunningInstances();
            $process = $this->runJob($job);

            $job->calculateNextRun();
            $job->setLastUse($now);

            $this->entityManager->persist($job);
            $this->entityManager->flush();

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
            $this->waitProcesses(...$processes);

            $style->success('All jobs are finished.');
        } else {
            $style->info('No jobs were executed. See reasons below.');
        }

        return Command::SUCCESS;
    }

    private function waitProcesses(CronJobRunning ...$processes): void
    {
        while (count($processes) > 0) {
            foreach ($processes as $key => $running) {
                try {
                    $running->process->checkTimeout();

                    if ($running->process->isRunning() === true) {
                        break;
                    }
                } catch (ProcessTimedOutException $e) {
                }

                $job = $running->cronJob->decreaseRunningInstances();

                $this->entityManager->persist($job);
                $this->entityManager->flush();

                unset($processes[$key]);
            }

            sleep(1);
        }
    }

    private function runJob(CronJob $job): Process
    {
        $command = [
            $this->commandHelper->getPhpExecutable(),
            $this->commandHelper->getConsoleBin(),
            'shapecode:cron:process',
            $job->getId(),
        ];

        $process = new Process($command);
        $process->disableOutput();

        $timeout = $this->commandHelper->getTimeout();
        if ($timeout > 0) {
            $process->setTimeout($timeout);
        }

        $process->start();

        return $process;
    }
}
