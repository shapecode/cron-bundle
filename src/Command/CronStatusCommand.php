<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Command;

use Shapecode\Bundle\CronBundle\Console\Style\CronStyle;
use Shapecode\Bundle\CronBundle\Repository\CronJobRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'shapecode:cron:status',
    description: 'Displays the current status of cron jobs'
)]
final class CronStatusCommand extends Command
{
    public function __construct(
        private readonly CronJobRepository $cronJobRepository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new CronStyle($input, $output);

        $io->title('Cron job status');

        $cronJobs = $this->cronJobRepository->findAll();

        $tableContent = [];
        foreach ($cronJobs as $cronJob) {
            $row = [
                $cronJob->getId(),
                $cronJob->getFullCommand(),
            ];

            if (! $cronJob->isEnable()) {
                $row[] = 'Not scheduled';
            } else {
                $row[] = $cronJob->getNextRun()->format('r');
            }

            if ($cronJob->getLastUse() !== null) {
                $row[] = $cronJob->getLastUse()->format('r');
            } else {
                $row[] = 'This job has not yet been run';
            }

            $row[] = $cronJob->isEnable() ? 'Enabled' : 'Disabled';

            $tableContent[] = $row;
        }

        $header = ['ID', 'Command', 'Next Schedule', 'Last run', 'Enabled'];
        $io->table($header, $tableContent);

        return Command::SUCCESS;
    }
}
