<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Shapecode\Bundle\CronBundle\Collection\CronJobRunningCollection;
use Shapecode\Bundle\CronBundle\Console\Style\CronStyle;
use Shapecode\Bundle\CronBundle\CronJob\CommandHelper;
use Shapecode\Bundle\CronBundle\Domain\CronJobRunning;
use Shapecode\Bundle\CronBundle\Entity\CronJob;
use Shapecode\Bundle\CronBundle\Infrastructure\Clock;
use Shapecode\Bundle\CronBundle\Repository\CronJobRepository;
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
    name: CronRunCommand::NAME,
    description: 'Runs any currently schedule cron jobs'
)]
final class CronRunCommand extends Command
{
    public const NAME = 'shapecode:cron:run';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CronJobRepository $cronJobRepository,
        private readonly CommandHelper $commandHelper,
        private readonly Clock $clock
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new CronStyle($input, $output);
        $now   = $this->clock->now();

        $jobsToRun = $this->cronJobRepository->findAll();

        $jobCount = count($jobsToRun);
        $style->comment(sprintf('Cron jobs started at %s', $now->format('r')));

        $style->title('Execute cron jobs');
        $style->info(sprintf('Found %d jobs', $jobCount));

        $processes = new CronJobRunningCollection();

        foreach ($jobsToRun as $job) {
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

            $processes->add(new CronJobRunning($job, $process));

            if ($job->getRunningInstances() > $job->getMaxInstances()) {
                $style->notice('cronjob will not be executed. The number of maximum instances has been exceeded.');
            } else {
                $style->success('cronjob started successfully and is running in background');
            }
        }

        $style->section('Summary');

        if ($processes->isEmpty()) {
            $style->info('No jobs were executed.');

            return Command::SUCCESS;
        }

        $style->text('waiting for all running jobs ...');

        $this->waitProcesses($processes);

        $style->success('All jobs are finished.');

        return Command::SUCCESS;
    }

    private function waitProcesses(CronJobRunningCollection $processes): void
    {
        while (count($processes) > 0) {
            foreach ($processes as $running) {
                try {
                    $running->process->checkTimeout();

                    if ($running->process->isRunning() === true) {
                        break;
                    }
                } catch (ProcessTimedOutException) {
                }

                $job = $running->cronJob->decreaseRunningInstances();

                $this->entityManager->persist($job);
                $this->entityManager->flush();

                $processes->remove($running);
            }

            sleep(1);
        }
    }

    private function runJob(CronJob $job): Process
    {
        $command = [
            $this->commandHelper->getPhpExecutable(),
            $this->commandHelper->getConsoleBin(),
            CronProcessCommand::NAME,
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
