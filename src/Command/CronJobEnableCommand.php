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
use Symfony\Component\Console\Output\OutputInterface;

use function assert;
use function count;
use function is_string;
use function sprintf;

#[AsCommand(
    name: 'shapecode:cron:enable',
    description: 'Enables a cronjob'
)]
final class CronJobEnableCommand extends Command
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
            ->addArgument('job', InputArgument::REQUIRED, 'Name or id of the job to disable');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new CronStyle($input, $output);

        $nameOrId = $input->getArgument('job');
        assert(is_string($nameOrId));

        $jobs = $this->cronJobRepository->findByCommandOrId($nameOrId);

        if (count($jobs) === 0) {
            $io->error(sprintf('Couldn\'t find a job by the name or id of %s', $nameOrId));

            return Command::FAILURE;
        }

        foreach ($jobs as $job) {
            $job->enable();
            $this->entityManager->persist($job);
        }

        $this->entityManager->flush();

        $io->success('cron enabled');

        return Command::SUCCESS;
    }
}
