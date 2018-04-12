<?php

namespace Shapecode\Bundle\CronBundle\Command;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Persistence\ManagerRegistry;
use Shapecode\Bundle\CronBundle\Entity\CronJobInterface;
use Shapecode\Bundle\CronBundle\Entity\CronJobResult;
use Shapecode\Bundle\CronBundle\Manager\CronJobManagerInterface;
use Shapecode\Bundle\CronBundle\Model\CronJobMetadata;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\KernelInterface;

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
     * @param KernelInterface         $kernel
     * @param Reader                  $annotationReader
     * @param ManagerRegistry         $registry
     * @param RequestStack            $requestStack
     */
    public function __construct(
        CronJobManagerInterface $manager,
        KernelInterface $kernel,
        Reader $annotationReader,
        ManagerRegistry $registry,
        RequestStack $requestStack)
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
        $keepDeleted = $input->getOption("keep-deleted");
        $defaultDisabled = $input->getOption("default-disabled");

        // Enumerate the known jobs
        $jobRepo = $this->getCronJobRepository();
        $knownJobs = $jobRepo->getKnownJobs()->toArray();
        $em = $this->getManager();

        $counter = [];
        foreach ($this->getCronManager()->getJobs() as $jobMetadata) {
            $command = $jobMetadata->getCommand();

            if (!isset($counter[$command])) {
                $counter[$command] = 0;
            }

            $counter[$command]++;

            if (in_array($command, $knownJobs)) {
                // Clear it from the known jobs so that we don't try to delete it
                unset($knownJobs[array_search($command, $knownJobs)]);

                // Update the job if necessary
                $currentJob = $jobRepo->findOneByCommand($command, $counter[$command]);
                $currentJob->setDescription($jobMetadata->getDescription());

                if ($currentJob->getPeriod() != $jobMetadata->getClearedExpression()) {
                    $currentJob->setPeriod($jobMetadata->getClearedExpression());
                    $currentJob->calculateNextRun();
                    $output->writeln('Updated interval for ' . $command . ' to ' . $jobMetadata->getClearedExpression());
                } else {
                    $output->writeln('Updated for ' . $command . ' not needed');
                }
            } else {
                $this->newJobFound($output, $jobMetadata, $defaultDisabled, $counter[$command]);
            }
        }

        // Clear any jobs that weren't found
        if (!$keepDeleted) {
            foreach ($knownJobs as $deletedJob) {
                $output->writeln('Deleting job: ' . $deletedJob);
                $jobsToDelete = $jobRepo->findByCommand($deletedJob);
                foreach ($jobsToDelete as $jobToDelete) {
                    $em->remove($jobToDelete);
                }
            }
        }

        $em->flush();
        $output->writeln("Finished scanning for cron jobs");

        return CronJobResult::SUCCEEDED;
    }

    /**
     * @param OutputInterface $output
     * @param CronJobMetadata $metadata
     * @param bool            $defaultDisabled
     * @param                 $counter
     */
    protected function newJobFound(OutputInterface $output, CronJobMetadata $metadata, $defaultDisabled = false, $counter)
    {
        $className = $this->getCronJobRepository()->getClassName();

        /** @var CronJobInterface $newJob */
        $newJob = new $className();
        $newJob->setCommand($metadata->getCommand());
        $newJob->setDescription($metadata->getDescription());
        $newJob->setPeriod($metadata->getClearedExpression());
        $newJob->setEnable(!$defaultDisabled);
        $newJob->setNumber($counter);
        $newJob->calculateNextRun();

        $output->writeln("Added the job " . $newJob->getCommand() . " with period " . $newJob->getPeriod());

        $this->getManager()->persist($newJob);
    }

    /**
     * @return CronJobManagerInterface
     */
    protected function getCronManager()
    {
        return $this->cronJobManager;
    }
}
