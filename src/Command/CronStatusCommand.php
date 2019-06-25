<?php

namespace Shapecode\Bundle\CronBundle\Command;

use Shapecode\Bundle\CronBundle\Console\Style\CronStyle;
use Shapecode\Bundle\CronBundle\Entity\CronJobInterface;
use Shapecode\Bundle\CronBundle\Entity\CronJobResult;
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

    /**
     * @inheritdoc
     */
    protected function configure(): void
    {
        $this->setName('shapecode:cron:status');
        $this->setDescription('Displays the current status of cron jobs');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $style = new CronStyle($input, $output);
        $jobRepo = $this->getCronJobRepository();

        $style->title('Cron job status');

        /** @var CronJobInterface[] $cronJobs */
        $cronJobs = $jobRepo->findAll();

        $tableContent = [];
        foreach ($cronJobs as $cronJob) {
            $row = [
                $cronJob->getId(),
                $cronJob->getFullCommand(),
            ];

            if (!$cronJob->isEnable()) {
                $row[] = 'Not scheduled';
            } else {
                $row[] = $cronJob->getNextRun()->format('r');
            }

            if ($cronJob->getLastUse()) {
                $row[] = $cronJob->getLastUse()->format('r');
            } else {
                $row[] = 'This job has not yet been run';
            }

            $row[] = $cronJob->isEnable() ? 'Enabled' : 'Disabled';

            $tableContent[] = $row;
        }

        $header = ['ID', 'Command', 'Next Schedule', 'Last run', 'Enabled'];
        $style->table($header, $tableContent);

        return CronJobResult::EXIT_CODE_SUCCEEDED;
    }
}
