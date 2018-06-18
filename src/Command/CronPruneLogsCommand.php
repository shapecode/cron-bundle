<?php

namespace Shapecode\Bundle\CronBundle\Command;

use Shapecode\Bundle\CronBundle\Entity\CronJobResult;
use Shapecode\Bundle\CronBundle\Service\CronJobResultServiceInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CronPruneLogsCommand
 *
 * @package Shapecode\Bundle\CronBundle\Command
 * @author  Nikita Loges
 */
class CronPruneLogsCommand extends Command
{

    /** @var CronJobResultServiceInterface */
    protected $resultService;

    /**
     * @param CronJobResultServiceInterface $resultService
     */
    public function __construct(CronJobResultServiceInterface $resultService)
    {
        parent::__construct();

        $this->resultService = $resultService;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('shapecode:cron:result:prune');

        $this->setAliases([
            'shapecode:cron:losg:clean-up'
        ]);

        $this->setDescription('Cleans the logs for each cron job.');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Cleaning logs for all cron jobs");

        $this->resultService->prune();

        $output->writeln("Logs cleaned successfully");

        return CronJobResult::EXIT_CODE_SUCCEEDED;
    }
}
