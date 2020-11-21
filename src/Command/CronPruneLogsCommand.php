<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Command;

use Shapecode\Bundle\CronBundle\Entity\CronJobResult;
use Shapecode\Bundle\CronBundle\Service\CronJobResultServiceInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CronPruneLogsCommand extends Command
{
    /** @var CronJobResultServiceInterface */
    private $resultService;

    public function __construct(CronJobResultServiceInterface $resultService)
    {
        parent::__construct();

        $this->resultService = $resultService;
    }

    protected function configure(): void
    {
        $this->setName('shapecode:cron:result:prune');

        $this->setAliases([
            'shapecode:cron:logs:clean-up',
        ]);

        $this->setDescription('Cleans the logs for each cron job.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Cleaning logs for all cron jobs');

        $this->resultService->prune();

        $output->writeln('Logs cleaned successfully');

        return CronJobResult::EXIT_CODE_SUCCEEDED;
    }
}
