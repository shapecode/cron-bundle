<?php

namespace Shapecode\Bundle\CronBundle\Command;

use Doctrine\ORM\EntityRepository;
use Shapecode\Bundle\CronBundle\Entity\CronJobResult;
use Shapecode\Bundle\CronBundle\Entity\Interfaces\CronJobInterface;
use Shapecode\Bundle\CronBundle\Entity\Interfaces\CronJobResultInterface;
use Shapecode\Bundle\CronBundle\Manager\CronJobManagerInterface;
use Shapecode\Bundle\CronBundle\Repository\Interfaces\CronJobRepositoryInterface;
use Shapecode\Bundle\CronBundle\Repository\Interfaces\CronJobResultRepositoryInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CronScanCommand
 *
 * @package Shapecode\Bundle\CronBundle\Command
 * @author  Nikita Loges
 */
class CronScanCommand extends Command
{

    /** @var RegistryInterface */
    protected $doctrine;

    /** @var CronJobManagerInterface */
    protected $cronManager;

    /**
     * @param RegistryInterface       $doctrine
     * @param CronJobManagerInterface $cronManager
     */
    public function __construct(RegistryInterface $doctrine, CronJobManagerInterface $cronManager)
    {
        $this->doctrine = $doctrine;
        $this->cronManager = $cronManager;

        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        parent::configure();

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
        $em = $this->getEntityManager($jobRepo->getClassName());

        $counter = [];
        foreach ($this->cronManager->getJobs() as $jobMetadata) {
            $command = $jobMetadata->getCommand();
            $name = $command->getName();

            $schedule = $jobMetadata->getExpression();
            $schedule = str_replace('\\', '', $schedule);

            if (!isset($counter[$name])) {
                $counter[$name] = 0;
            }

            $counter[$name]++;

            if (in_array($name, $knownJobs)) {
                // Clear it from the known jobs so that we don't try to delete it
                unset($knownJobs[array_search($name, $knownJobs)]);

                // Update the job if necessary
                $currentJob = $jobRepo->findOneByCommand($name, $counter[$name]);
                $currentJob->setDescription($command->getDescription());

                if ($currentJob->getPeriod() != $schedule) {
                    $currentJob->setPeriod($schedule);
                    $currentJob->calculateNextRun();
                    $output->writeln('Updated interval for ' . $name . ' to ' . $schedule);
                }
            } else {
                $this->newJobFound($output, $command, $schedule, $defaultDisabled, $counter[$name]);
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
     * @param OutputInterface   $output
     * @param Command           $command
     * @param string            $schedule
     * @param bool              $defaultDisabled
     * @param                   $counter
     */
    protected function newJobFound(OutputInterface $output, Command $command, $schedule, $defaultDisabled = false, $counter)
    {
        $className = $this->getCronJobRepository()->getClassName();

        /** @var CronJobInterface $newJob */
        $newJob = new $className();
        $newJob->setCommand($command->getName());
        $newJob->setDescription($command->getDescription());
        $newJob->setPeriod($schedule);
        $newJob->setEnable(!$defaultDisabled);
        $newJob->setNumber($counter);
        $newJob->calculateNextRun();

        $output->writeln("Added the job " . $newJob->getCommand() . " with period " . $newJob->getPeriod());

        $this->getEntityManager($className)->persist($newJob);
    }

    /**
     * @return RegistryInterface
     */
    protected function getDoctrine()
    {
        return $this->doctrine;
    }

    /**
     * @param null $className
     *
     * @return \Doctrine\ORM\EntityManager|null
     */
    protected function getEntityManager($className = null)
    {
        if (is_object($className)) {
            $className = get_class($className);
        }

        if (is_null($className)) {
            return $this->getDoctrine()->getEntityManager();
        }

        return $this->getDoctrine()->getEntityManagerForClass($className);
    }

    /**
     * @param $className
     *
     * @return \Doctrine\Common\Persistence\ObjectRepository|EntityRepository
     */
    protected function findRepository($className)
    {
        return $this->getDoctrine()->getRepository($className);
    }

    /**
     * @return EntityRepository|CronJobRepositoryInterface
     */
    protected function getCronJobRepository()
    {
        return $this->findRepository(CronJobInterface::class);
    }

    /**
     * @return EntityRepository|CronJobResultRepositoryInterface
     */
    protected function getCronJobResultRepository()
    {
        return $this->findRepository(CronJobResultInterface::class);
    }
}
