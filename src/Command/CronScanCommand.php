<?php

namespace Shapecode\Bundle\CronBundle\Command;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Persistence\ManagerRegistry;
use Shapecode\Bundle\CronBundle\Console\Style\CronStyle;
use Shapecode\Bundle\CronBundle\Entity\CronJobInterface;
use Shapecode\Bundle\CronBundle\Entity\CronJobResult;
use Shapecode\Bundle\CronBundle\Manager\CronJobManagerInterface;
use Shapecode\Bundle\CronBundle\Model\CronJobMetadata;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Class CronScanCommand
 *
 * @package Shapecode\Bundle\CronBundle\Command
 * @author  Nikita Loges
 */
class CronScanCommand extends BaseCommand
{

    /** @var CronJobManagerInterface */
    protected $cronJobManager;

    /**
     * @param CronJobManagerInterface $manager
     * @param Kernel                  $kernel
     * @param Reader                  $annotationReader
     * @param ManagerRegistry         $registry
     * @param RequestStack            $requestStack
     */
    public function __construct(
        CronJobManagerInterface $manager,
        Kernel $kernel,
        Reader $annotationReader,
        ManagerRegistry $registry,
        RequestStack $requestStack
    )
    {
        $this->cronJobManager = $manager;

        parent::__construct($kernel, $annotationReader, $registry, $requestStack);
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('shapecode:cron:scan');
        $this->setDescription('Scans for any new or deleted cron jobs');

        $this->addOption('keep-deleted', 'k', InputOption::VALUE_NONE, 'If set, deleted cron jobs will not be removed');
        $this->addOption('default-disabled', 'd', InputOption::VALUE_NONE, 'If set, new jobs will be disabled by default');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $style = new CronStyle($input, $output);
        $style->comment('Scan for cronjobs started at ' . (new \DateTime())->format('r'));
        $style->title('scanning ...');

        $keepDeleted = $input->getOption('keep-deleted');
        $defaultDisabled = $input->getOption('default-disabled');

        // Enumerate the known jobs
        $jobRepo = $this->getCronJobRepository();
        $knownJobs = $jobRepo->getKnownJobs()->toArray();
        $em = $this->getManager();

        $counter = [];
        foreach ($this->getCronManager()->getJobs() as $jobMetadata) {
            $command = $jobMetadata->getCommand();

            $style->section($command);

            if (!isset($counter[$command])) {
                $counter[$command] = 0;
            }

            $counter[$command]++;

            if (\in_array($command, $knownJobs, true)) {
                // Clear it from the known jobs so that we don't try to delete it
                unset($knownJobs[\array_search($command, $knownJobs, true)]);

                // Update the job if necessary
                $currentJob = $jobRepo->findOneByCommand($command, $counter[$command]);
                $currentJob->setDescription($jobMetadata->getDescription());
                $currentJob->setArguments($jobMetadata->getArguments());

                $style->text('command: ' . $jobMetadata->getCommand());
                $style->text('arguments: ' . $jobMetadata->getArguments());
                $style->text('expression: ' . $jobMetadata->getClearedExpression());

                if ($currentJob->getPeriod() !== $jobMetadata->getClearedExpression()) {
                    $oldExpression = $currentJob->getPeriod();

                    $currentJob->setPeriod($jobMetadata->getClearedExpression());
                    $currentJob->calculateNextRun();
                    $style->notice('interval updated form ' . $oldExpression . ' to ' . $currentJob->getPeriod());
                }
            } else {
                $this->newJobFound($style, $jobMetadata, $defaultDisabled, $counter[$command]);
            }
        }

        $style->success('Finished scanning for cronjobs');

        // Clear any jobs that weren't found
        if (!$keepDeleted) {
            $style->title('remove cronjobs');

            if (\count($knownJobs)) {
                foreach ($knownJobs as $deletedJob) {
                    $style->notice('Deleting job: ' . $deletedJob);
                    $jobsToDelete = $jobRepo->findByCommand($deletedJob);
                    foreach ($jobsToDelete as $jobToDelete) {
                        $em->remove($jobToDelete);
                    }
                }
            } else {
                $style->info('No cronjob has to be removed.');
            }
        }

        $em->flush();

        return CronJobResult::EXIT_CODE_SUCCEEDED;
    }

    /**
     * @param CronStyle       $output
     * @param CronJobMetadata $metadata
     * @param bool            $defaultDisabled
     * @param int             $counter
     */
    protected function newJobFound(CronStyle $output, CronJobMetadata $metadata, bool $defaultDisabled = false, int $counter): void
    {
        $className = $this->getCronJobRepository()->getClassName();

        /** @var CronJobInterface $newJob */
        $newJob = new $className();
        $newJob->setCommand($metadata->getCommand());
        $newJob->setArguments($metadata->getArguments());
        $newJob->setDescription($metadata->getDescription());
        $newJob->setPeriod($metadata->getClearedExpression());
        $newJob->setEnable(!$defaultDisabled);
        $newJob->setNumber($counter);
        $newJob->calculateNextRun();

        $message = sprintf('Found new job: "%s" with period %s', $newJob->getFullCommand(), $newJob->getPeriod());
        $output->success($message);

        $this->getManager()->persist($newJob);
    }

    /**
     * @return CronJobManagerInterface
     */
    protected function getCronManager(): CronJobManagerInterface
    {
        return $this->cronJobManager;
    }
}
