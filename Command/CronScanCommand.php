<?php

namespace Shapecode\Bundle\CronBundle\Command;

use Shapecode\Bundle\CronBundle\Annotation\CronJob as CronJobAnnotation;
use Shapecode\Bundle\CronBundle\Entity\CronJob;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CronScanCommand
 * @package Shapecode\Bundle\CronBundle\Command
 * @author Nikita Loges
 */
class CronScanCommand extends BaseCommand
{
    /** @inheritdoc */
    protected $commandName = 'shapecode:cron:scan';

    /** @inheritdoc */
    protected $commandDescription = 'Scans for any new or deleted cron jobs';

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        parent::configure();

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

        // Enumerate all the jobs currently loaded
        $reader = $this->getReader();

        foreach ($this->getApplication()->all() as $command) {
            // Check for an @CronJob annotation
            $reflClass = new \ReflectionClass($command);

            foreach ($reader->getClassAnnotations($reflClass) as $annotation) {
                if ($annotation instanceof CronJobAnnotation) {
                    $job = $command->getName();

                    if (in_array($job, $knownJobs)) {
                        // Clear it from the known jobs so that we don't try to delete it
                        unset($knownJobs[array_search($job, $knownJobs)]);

                        // Update the job if necessary
                        $currentJob = $jobRepo->findOneByCommand($job);
                        $currentJob->setDescription($command->getDescription());

                        if ($currentJob->getPeriod() != $annotation->value) {
                            $currentJob->setPeriod($annotation->value);
                            $currentJob->calculateNextRun();
                            $output->writeln('Updated interval for ' . $job . ' to ' . $annotation->value);
                        }
                    } else {
                        $this->newJobFound($output, $command, $annotation, $defaultDisabled);
                    }
                }
            }
        }

        // Clear any jobs that weren't found
        if (!$keepDeleted) {
            foreach ($knownJobs as $deletedJob) {
                $output->writeln('Deleting job: ' . $deletedJob);
                $jobToDelete = $jobRepo->findOneByCommand($deletedJob);
                $this->getEntityManager()->remove($jobToDelete);
            }
        }

        $this->getEntityManager()->flush();
        $output->writeln("Finished scanning for cron jobs");
    }

    /**
     * @param OutputInterface $output
     * @param Command $command
     * @param CronJobAnnotation $annotation
     * @param bool $defaultDisabled
     */
    protected function newJobFound(OutputInterface $output, Command $command, CronJobAnnotation $annotation, $defaultDisabled = false)
    {
        $newJob = new CronJob();
        $newJob->setCommand($command->getName());
        $newJob->setDescription($command->getDescription());
        $newJob->setPeriod($annotation->value);
        $newJob->setEnable(!$defaultDisabled);
        $newJob->calculateNextRun();

        $output->writeln("Added the job " . $newJob->getCommand() . " with period " . $newJob->getPeriod());
        $this->getEntityManager()->persist($newJob);
    }
}
