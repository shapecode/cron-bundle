<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Shapecode\Bundle\CronBundle\Console\Style\CronStyle;
use Shapecode\Bundle\CronBundle\Repository\CronJobRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function assert;
use function count;
use function is_string;
use function sprintf;

#[AsCommand(
    name: 'shapecode:cron:edit',
    description: 'Changes the status of a cron job'
)]
final class CronJobEditCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CronJobRepository $cronJobRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('job', InputArgument::REQUIRED, 'Name of the job to disable')
            ->addOption('enable', null, InputOption::VALUE_REQUIRED, 'Enable or disable this cron (y or n)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new CronStyle($input, $output);

        $jobName = $input->getArgument('job');
        assert(is_string($jobName));

        $jobs = $this->cronJobRepository->findByCommand($jobName);

        if (count($jobs) === 0) {
            $io->error(sprintf('Couldn\'t find a job by the name of %s', $jobName));

            return Command::FAILURE;
        }

        $enable = $input->getOption('enable') === 'y';

        foreach ($jobs as $job) {
            $job->setEnable($enable);
            $this->entityManager->persist($job);
        }

        $this->entityManager->flush();

        if ($enable) {
            $io->success('cron enabled');
        } else {
            $io->success('cron disabled');
        }

        return Command::SUCCESS;
    }
}
