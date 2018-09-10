<?php

namespace Shapecode\Bundle\CronBundle\Command;

use Shapecode\Bundle\CronBundle\Console\Style\CronStyle;
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

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('shapecode:cron:edit');
        $this->setDescription('Changes the status of a cron job');

        $this->addArgument("job", InputArgument::REQUIRED, 'Name of the job to disable');
        $this->addOption('enable', null, InputOption::VALUE_REQUIRED, 'Enable or disable this cron (y or n)');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $style = new CronStyle($input, $output);

        $jobName = $input->getArgument('job');
        $jobRepo = $this->getCronJobRepository();
        $jobs = $jobRepo->findByCommand($jobName);

        $em = $this->getManager();

        if (!\count($jobs)) {
            $style->error("Couldn't find a job by the name of " . $jobName);

            return CronJobResult::EXIT_CODE_FAILED;
        }

        $enable = $input->getOption('enable') === 'y';

        foreach ($jobs as $job) {
            $job->setEnable($enable);
            $em->persist($job);
        }

        $em->flush();

        if ($enable) {
            $style->success('cron enabled');
        } else {
            $style->success('cron disabled');
        }

        return CronJobResult::EXIT_CODE_SUCCEEDED;
    }
}
