<?php

namespace Shapecode\Bundle\CronBundle\Command;

use Doctrine\ORM\EntityRepository;
use Shapecode\Bundle\CronBundle\Entity\Interfaces\CronJobInterface;
use Shapecode\Bundle\CronBundle\Entity\Interfaces\CronJobResultInterface;
use Shapecode\Bundle\CronBundle\Repository\Interfaces\CronJobRepositoryInterface;
use Shapecode\Bundle\CronBundle\Repository\Interfaces\CronJobResultRepositoryInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CronProcessCommand
 *
 * @package Shapecode\Bundle\CronBundle\Command
 * @author  Nikita Loges
 * @company tenolo GbR
 */
class CronProcessCommand extends BaseCommand
{

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('shapecode:cron:process');

        $this->addArgument('cron', InputArgument::REQUIRED);
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var CronJobInterface $job */
        $job = $this->getCronJobRepository()->find($input->getArgument('cron'));

        $command = $job->getCommand();
        $watch = 'job-' . $command;

        $output->write("Running " . $job->getCommand() . ": ");

        try {
            $commandToRun = $this->getApplication()->get($job->getCommand());
        } catch (\InvalidArgumentException $ex) {
            $output->writeln(' skipped (command no longer exists)');
            $this->recordJobResult($job, 0, 'Command no longer exists', CronJobResultInterface::SKIPPED);

            // No need to reschedule non-existant commands
            return;
        }

        $emptyInput = new ArrayInput([
            'command' => $job->getCommand()
        ]);
        $jobOutput = new BufferedOutput();

        $this->getStopWatch()->start($watch);

        try {
            $statusCode = $commandToRun->run($emptyInput, $jobOutput);
        } catch (\Exception $ex) {
            $statusCode = CronJobResultInterface::FAILED;
            $jobOutput->writeln('');
            $jobOutput->writeln('Job execution failed with exception ' . get_class($ex) . ':');
        }
        $this->getStopWatch()->stop($watch);

        if (is_null($statusCode)) {
            $statusCode = 0;
        }

        $statusStr = CronJobResultInterface::FAILED;
        switch ($statusCode) {
            case 0:
                $statusStr = CronJobResultInterface::SUCCEEDED;
                break;
            case 2:
                $statusStr = CronJobResultInterface::SKIPPED;
                break;
        }

        $bufferedOutput = $jobOutput->fetch();
        $output->write($bufferedOutput);

        $duration = $this->getStopWatch()->getEvent($watch)->getDuration();
        $output->writeln($statusStr . ' in ' . number_format(($duration / 1000), 4) . ' seconds');

        // Record the result
        $this->recordJobResult($job, $duration, $bufferedOutput, $statusCode);
    }

    /**
     * @param CronJobInterface $job
     * @param                  $timeTaken
     * @param                  $output
     * @param                  $statusCode
     */
    protected function recordJobResult(CronJobInterface $job, $timeTaken, $output, $statusCode)
    {
        $repo = $this->getCronJobResultRepository();
        $manager = $this->getEntityManager($repo->getClassName());

        $job = $manager->find(CronJobInterface::class, $job->getId());

        $className = $this->getCronJobResultRepository()->getClassName();

        /** @var CronJobResultInterface $result */
        $result = new $className();
        $result->setCronJob($job);
        $result->setRunTime($timeTaken);
        $result->setOutput($output);
        $result->setStatusCode($statusCode);

        $manager->persist($result);
        $manager->flush();
    }

    /**
     * @return RegistryInterface
     */
    protected function getDoctrine()
    {
        return $this->getContainer()->get('doctrine');
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
