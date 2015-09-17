<?php

namespace Shapecode\Bundle\CronBundle\Command;

use Shapecode\Bundle\CronBundle\Entity\CronJobResult;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CronPruneLogsCommand
 * @package Shapecode\Bundle\CronBundle\Command
 * @author Nikita Loges
 */
class CronPruneLogsCommand extends BaseCommand
{

    /** @inheritdoc */
    protected $commandName = 'shapecode:cron:logs:cleanup';

    /** @inheritdoc */
    protected $commandDescription = 'Cleans the logs for each cron job, leaving only recent failures and the most recent success';

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        parent::configure();

        $this->addArgument('job', InputArgument::OPTIONAL, 'Operate only on this job');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $job = $input->getArgument('job');

        if ($job) {
            $output->writeln("Cleaning logs for cron job $job");
            $jobObj = $this->getCronJobRepository()->findOneByCommand($job);

            if (!$jobObj) {

                $output->writeln("Couldn't find a job by the name of " . $job);

                return CronJobResult::FAILED;
            }

            $this->getCronJobResultRepository()->deleteOldLogs($jobObj);
        } else {
            $output->writeln("Cleaning logs for all cron jobs");
            $this->getCronJobResultRepository()->deleteOldLogs();
        }

        $output->writeln("Logs cleaned successfully");

        return CronJobResult::SUCCEEDED;
    }
}
