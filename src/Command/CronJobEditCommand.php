<?php

namespace Shapecode\Bundle\CronBundle\Command;

use Shapecode\Bundle\CronBundle\Entity\CronJobResult;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CronJobEditCommand
 *
 * @package Shapecode\Bundle\CronBundle\Command
 * @author  Nikita Loges
 */
class CronJobEditCommand extends BaseCommand
{

    /** @var string */
    protected $commandName = 'shapecode:cron:edit';

    /** @var string */
    protected $commandDescription = 'Changes the status of a cron job';

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        parent::configure();

        $this->addArgument("job", InputArgument::REQUIRED, 'Name of the job to disable');
        $this->addOption('enable', 'e', InputOption::VALUE_REQUIRED, 'Enable or disable this cron (y or n)');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $jobName = $input->getArgument('job');
        $jobRepo = $this->getCronJobRepository();
        $jobs = $jobRepo->findByCommand($jobName);

        if (!count($jobs)) {
            $output->writeln("Couldn't find a job by the name of " . $jobName);

            return CronJobResult::FAILED;
        }

        $enable = ($input->getOption('enable') == 'y') ? true : false;

        foreach ($jobs as $job) {
            $job->setEnable($enable);
            $this->getEntityManager()->persist($job);
        }

        $this->getEntityManager()->flush();

        if ($enable) {
            $output->writeln('cron enabled');
        } else {
            $output->writeln('cron disabled');
        }

        return CronJobResult::SUCCEEDED;
    }
}
