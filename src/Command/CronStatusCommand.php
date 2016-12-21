<?php

namespace Shapecode\Bundle\CronBundle\Command;

use Shapecode\Bundle\CronBundle\Entity\CronJob;
use Shapecode\Bundle\CronBundle\Entity\CronJobResult;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CronStatusCommand
 *
 * @package Shapecode\Bundle\CronBundle\Command
 * @author  Nikita Loges
 */
class CronStatusCommand extends BaseCommand
{
    /** @inheritdoc */
    protected $commandName = 'shapecode:cron:status';

    /** @inheritdoc */
    protected $commandDescription = 'Displays the current status of cron jobs';

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        parent::configure();

        $this->addArgument('job', InputArgument::OPTIONAL, 'Show information for only this job');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $jobRepo = $this->getCronJobRepository();
        $resultRepo = $this->getCronJobResultRepository();

        $output->writeln('Cron job statuses:');

        if ($jobName = $input->getArgument('job')) {
            try {
                $cronJobs = [$jobRepo->findOneByCommand($jobName)];
            } catch (\Exception $e) {
                $output->writeln('Couldn\'t find a job by the name of ' . $jobName);

                return CronJobResult::FAILED;
            }
        } else {
            /** @var CronJob[] $cronJobs */
            $cronJobs = $jobRepo->findAll();
        }

        foreach ($cronJobs as $cronJob) {
            $output->write(" - " . $cronJob->getCommand());

            if (!$cronJob->isEnable()) {
                $output->write(' (disabled)');
            }

            $output->writeln('');
            $output->writeln('   Description: ' . $cronJob->getDescription());

            if (!$cronJob->isEnable()) {
                $output->writeln('Not scheduled');
            } else {
                $output->write('Scheduled for: ');
                $now = new \DateTime();
                if ($cronJob->getNextRun() <= $now) {
                    $output->writeln('Next run');
                } else {
                    $output->writeln($cronJob->getNextRun()->format('r'));
                }
            }

            $mostRecent = $resultRepo->findMostRecent($cronJob);
            if ($mostRecent) {
                $output->writeln('Last run was: ' . $mostRecent->getOutput());
            } else {
                $output->writeln('This job has not yet been run');
            }

            $output->writeln('');
        }

        return CronJobResult::SUCCEEDED;
    }
}
