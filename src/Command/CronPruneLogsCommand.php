<?php

namespace Shapecode\Bundle\CronBundle\Command;

use Shapecode\Bundle\CronBundle\Entity\CronJobResult;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CronPruneLogsCommand
 *
 * @package Shapecode\Bundle\CronBundle\Command
 * @author  Nikita Loges
 */
class CronPruneLogsCommand extends BaseCommand
{

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('shapecode:cron:logs:cleanup');
        $this->setDescription('Cleans the logs for each cron job, leaving only recent failures and the most recent success');

        $this->addArgument('job', InputArgument::OPTIONAL, 'Operate only on this job');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $jobName = $input->getArgument('job');

        if ($jobName) {
            $output->writeln("Cleaning logs for cron job $jobName");
            $jobs = $this->getCronJobRepository()->findByCommand($jobName);

            if (!count($jobs)) {

                $output->writeln("Couldn't find a job by the name of " . $jobName);

                return CronJobResult::FAILED;
            }

            foreach ($jobs as $job) {
                $this->getCronJobResultRepository()->deleteOldLogs($job);
            }
        } else {
            $output->writeln("Cleaning logs for all cron jobs");
            $this->getCronJobResultRepository()->deleteOldLogs();
        }

        $output->writeln("Logs cleaned successfully");

        return CronJobResult::SUCCEEDED;
    }
}
