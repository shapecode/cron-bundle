<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Command;

use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Shapecode\Bundle\CronBundle\Console\Style\CronStyle;
use Shapecode\Bundle\CronBundle\Entity\CronJob;
use Shapecode\Bundle\CronBundle\Entity\CronJobResult;
use Shapecode\Bundle\CronBundle\Manager\CronJobManager;
use Shapecode\Bundle\CronBundle\Model\CronJobMetadata;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function array_search;
use function count;
use function in_array;
use function sprintf;

final class CronScanCommand extends BaseCommand
{
    private CronJobManager $cronJobManager;

    public function __construct(
        CronJobManager $manager,
        ManagerRegistry $registry
    ) {
        $this->cronJobManager = $manager;

        parent::__construct($registry);
    }

    protected function configure(): void
    {
        $this
            ->setName('shapecode:cron:scan')
            ->setDescription('Scans for any new or deleted cron jobs');

        $this
            ->addOption('keep-deleted', 'k', InputOption::VALUE_NONE, 'If set, deleted cron jobs will not be removed')
            ->addOption('default-disabled', 'd', InputOption::VALUE_NONE, 'If set, new jobs will be disabled by default');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new CronStyle($input, $output);
        $io->comment('Scan for cronjobs started at ' . (new DateTime())->format('r'));
        $io->title('scanning ...');

        $keepDeleted     = (bool) $input->getOption('keep-deleted');
        $defaultDisabled = (bool) $input->getOption('default-disabled');

        // Enumerate the known jobs
        $jobRepo   = $this->getCronJobRepository();
        $knownJobs = $jobRepo->getKnownJobs()->toArray();
        $em        = $this->getManager();

        $counter = [];
        foreach ($this->cronJobManager->getJobs() as $jobMetadata) {
            $command = $jobMetadata->getCommand();

            $io->section($command);

            if (! isset($counter[$command])) {
                $counter[$command] = 0;
            }

            $counter[$command]++;

            if (in_array($command, $knownJobs, true)) {
                // Clear it from the known jobs so that we don't try to delete it
                unset($knownJobs[array_search($command, $knownJobs, true)]);

                // Update the job if necessary
                $currentJob = $jobRepo->findOneByCommand($command, $counter[$command]);

                if ($currentJob === null) {
                    continue;
                }

                $currentJob->setDescription($jobMetadata->getDescription());
                $currentJob->setArguments($jobMetadata->getArguments());

                $io->text('command: ' . $jobMetadata->getCommand());
                $io->text('arguments: ' . $jobMetadata->getArguments());
                $io->text('expression: ' . $jobMetadata->getClearedExpression());
                $io->text('instances: ' . $jobMetadata->getMaxInstances());

                if (
                    $currentJob->getPeriod() !== $jobMetadata->getClearedExpression() ||
                    $currentJob->getMaxInstances() !== $jobMetadata->getMaxInstances() ||
                    $currentJob->getArguments() !== $jobMetadata->getArguments()
                ) {
                    $currentJob->setPeriod($jobMetadata->getClearedExpression());
                    $currentJob->setArguments($jobMetadata->getArguments());
                    $currentJob->setMaxInstances($jobMetadata->getMaxInstances());

                    $currentJob->calculateNextRun();
                    $io->notice('cronjob updated');
                }
            } else {
                $this->newJobFound($io, $jobMetadata, $defaultDisabled, $counter[$command]);
            }
        }

        $io->success('Finished scanning for cronjobs');

        // Clear any jobs that weren't found
        if ($keepDeleted === false) {
            $io->title('remove cronjobs');

            if (count($knownJobs) > 0) {
                foreach ($knownJobs as $deletedJob) {
                    $io->notice('Deleting job: ' . $deletedJob);
                    $jobsToDelete = $jobRepo->findByCommand($deletedJob);
                    foreach ($jobsToDelete as $jobToDelete) {
                        $em->remove($jobToDelete);
                    }
                }
            } else {
                $io->info('No cronjob has to be removed.');
            }
        }

        $em->flush();

        return CronJobResult::EXIT_CODE_SUCCEEDED;
    }

    private function newJobFound(CronStyle $io, CronJobMetadata $metadata, bool $defaultDisabled, int $counter): void
    {
        $newJob =
            CronJob::create(
                $metadata->getCommand(),
                $metadata->getClearedExpression()
            )
            ->setArguments($metadata->getArguments())
            ->setDescription($metadata->getDescription())
            ->setEnable(! $defaultDisabled)
            ->setNumber($counter)
            ->calculateNextRun();

        $message = sprintf('Found new job: "%s" with period %s', $newJob->getFullCommand(), $newJob->getPeriod());
        $io->success($message);

        $this->getManager()->persist($newJob);
    }
}
