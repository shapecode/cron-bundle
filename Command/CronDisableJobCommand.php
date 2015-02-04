<?php

namespace Shapecode\Bundle\CronBundle\Command;

use Shapecode\Bundle\CronBundle\Entity\CronJobResult;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CronDisableJobCommand
 * @package Shapecode\Bundle\CronBundle\Command
 * @author Nikita Loges
 */
class CronDisableJobCommand extends BaseCommand
{

    /** {@inheritdoc} */
    protected $commandName = 'shapecode:cron:disable-jon';

    /** {@inheritdoc} */
    protected $commandDescription = 'Disables a cron job';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this->addArgument("job", InputArgument::REQUIRED, 'Name of the job to disable');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $jobName = $input->getArgument('job');

        $jobRepo = $this->getCronJobRepository();

        $job = $jobRepo->findOneByCommand($jobName);

        if (!$job) {
            $output->writeln("Couldn't find a job by the name of " . $jobName);

            return CronJobResult::FAILED;
        }

        $job->setIsEnable(false);
        $this->getEntityManager()->persist($job);
        $this->getEntityManager()->flush();

        $output->writeln("Disabled cron job by the name of " . $jobName);
    }
}
