<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Command;

use Shapecode\Bundle\CronBundle\Console\Style\CronStyle;
use Shapecode\Bundle\CronBundle\Entity\CronJob;
use Shapecode\Bundle\CronBundle\Repository\CronJobRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function array_map;

#[AsCommand(
    name: 'shapecode:cron:status',
    description: 'Displays the current status of cron jobs',
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

        $tableContent = array_map(
            static fn (CronJob $cronJob): array => [
                $cronJob->getId(),
                $cronJob->getFullCommand(),
                $cronJob->isEnable() ? $cronJob->getNextRun()->format('r') : 'Not scheduled',
                $cronJob->getLastUse()?->format('r') ?? 'This job has not yet been run',
                $cronJob->isEnable() ? 'Enabled' : 'Disabled',
            ],
            $this->cronJobRepository->findAll(),
        );

        $io->table(
            [
                'ID',
                'Command',
                'Next Schedule',
                'Last run',
                'Enabled',
            ],
            $tableContent,
        );

        return Command::SUCCESS;
    }
}
