<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Command;

use Shapecode\Bundle\CronBundle\Console\Style\CronStyle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function assert;
use function count;
use function is_string;
use function sprintf;

final class CronJobEditCommand extends BaseCommand
{
    protected function configure(): void
    {
        $this->setName('shapecode:cron:edit');
        $this->setDescription('Changes the status of a cron job');

        $this->addArgument('job', InputArgument::REQUIRED, 'Name of the job to disable');
        $this->addOption('enable', null, InputOption::VALUE_REQUIRED, 'Enable or disable this cron (y or n)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new CronStyle($input, $output);

        $jobName = $input->getArgument('job');
        assert(is_string($jobName));

        $jobRepo = $this->getCronJobRepository();
        $jobs    = $jobRepo->findByCommand($jobName);

        $em = $this->getManager();

        if (count($jobs) === 0) {
            $io->error(sprintf('Couldn\'t find a job by the name of %s', $jobName));

            return Command::FAILURE;
        }

        $enable = $input->getOption('enable') === 'y';

        foreach ($jobs as $job) {
            $job->setEnable($enable);
            $em->persist($job);
        }

        $em->flush();

        if ($enable) {
            $io->success('cron enabled');
        } else {
            $io->success('cron disabled');
        }

        return Command::SUCCESS;
    }
}
