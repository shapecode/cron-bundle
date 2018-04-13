<?php

namespace Shapecode\Bundle\CronBundle\Command;

use Shapecode\Bundle\CronBundle\Entity\CronJobResult;
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
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Cleaning logs for all cron jobs");

        $this->getCronJobResultRepository()->deleteOldLogs();

        $output->writeln("Logs cleaned successfully");

        return CronJobResult::SUCCEEDED;
    }
}
