<?php

namespace Shapecode\Bundle\CronBundle\Manager;

use Doctrine\Common\Annotations\Reader;
use Shapecode\Bundle\CronBundle\Annotation\CronJob;
use Shapecode\Bundle\CronBundle\Model\CronJobMetadata;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class CronJobManager
 *
 * @package Shapecode\Bundle\CronBundle\Manager
 * @author  Nikita Loges
 * @company tenolo GbR
 */
class CronJobManager implements CronJobManagerInterface
{

    /** @var array */
    protected $applicationJobs = [];

    /** @var array */
    protected $jobs = [];

    /** @var KernelInterface */
    protected $kernel;

    /** @var Application */
    protected $application;

    /** @var Reader */
    protected $reader;

    /**
     * @param KernelInterface $kernel
     * @param Reader          $reader
     */
    public function __construct(KernelInterface $kernel, Reader $reader)
    {
        $this->kernel = $kernel;
        $this->application = new Application($kernel);
        $this->reader = $reader;
    }

    /**
     * @return mixed
     */
    public function getApplicationJobs()
    {
        if (is_null($this->applicationJobs)) {
            $this->applicationJobs = $this->initApplicationJobs();
        }

        return $this->applicationJobs;
    }

    /**
     * @return array
     */
    public function initApplicationJobs()
    {
        $applicationJobs = [];

        foreach ($this->getApplication()->all() as $command) {
            // Check for an @CronJob annotation
            $reflClass = new \ReflectionClass($command);

            foreach ($this->getReader()->getClassAnnotations($reflClass) as $annotation) {
                if ($annotation instanceof CronJob) {
                    $schedule = $annotation->value;

                    $meta = new CronJobMetadata($command, $schedule);
                    $applicationJobs[] = $meta;
                }
            }
        }

        return $applicationJobs;
    }

    /**
     * @param Command $command
     * @param         $expression
     */
    public function addJob(Command $command, $expression)
    {
        $this->jobs[] = new CronJobMetadata($command, $expression);
    }

    /**
     * @return array
     */
    public function getJobs()
    {
        $jobs = array_merge($this->jobs, $this->getApplicationJobs());

        return $jobs;
    }

    /**
     * @return Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @return Reader
     */
    public function getReader()
    {
        return $this->reader;
    }
}
