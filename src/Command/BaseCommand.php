<?php

namespace Shapecode\Bundle\CronBundle\Command;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityRepository;
use Shapecode\Bundle\CronBundle\Entity\CronJobInterface;
use Shapecode\Bundle\CronBundle\Entity\CronJobResultInterface;
use Shapecode\Bundle\CronBundle\Repository\CronJobRepositoryInterface;
use Shapecode\Bundle\CronBundle\Repository\CronJobResultRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Class BaseCronjob
 *
 * @package Shapecode\Bundle\CronBundle\Command
 * @author  Nikita Loges
 */
abstract class BaseCommand extends Command
{

    /** @var KernelInterface */
    protected $kernel;

    /** @var Reader */
    protected $annotationReader;

    /** @var ManagerRegistry */
    protected $registry;

    /** @var Stopwatch */
    protected $stopwatch;

    /** @var RequestStack */
    protected $requestStack;

    /** @var string|null */
    protected $environment;

    /**
     * @param KernelInterface $kernel
     * @param Reader          $annotationReader
     * @param ManagerRegistry $registry
     * @param RequestStack    $requestStack
     */
    public function __construct(KernelInterface $kernel, Reader $annotationReader, ManagerRegistry $registry, RequestStack $requestStack)
    {
        parent::__construct();

        $this->kernel = $kernel;
        $this->annotationReader = $annotationReader;
        $this->registry = $registry;
        $this->requestStack = $requestStack;
    }

    /**
     * @return KernelInterface
     */
    protected function getKernel()
    {
        return $this->kernel;
    }

    /**
     * @return Reader
     */
    public function getReader()
    {
        return $this->annotationReader;
    }

    /**
     * @return ManagerRegistry
     *
     * @deprecated
     */
    protected function getDoctrine()
    {
        return $this->getRegistry();
    }

    /**
     * @return ManagerRegistry
     */
    protected function getRegistry()
    {
        return $this->registry;
    }

    /**
     * @return Stopwatch
     */
    protected function getStopWatch()
    {
        if ($this->stopwatch === null) {
            $this->stopwatch = new Stopwatch();
        }

        return $this->stopwatch;
    }

    /**
     * @return null|\Symfony\Component\HttpFoundation\Request
     */
    protected function getRequest()
    {
        return $this->requestStack->getMasterRequest();
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectManager|null
     */
    protected function getManager()
    {
        return $this->getRegistry()->getManager();
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectRepository|EntityRepository|CronJobRepositoryInterface
     */
    protected function getCronJobRepository()
    {
        return $this->getRegistry()->getRepository(CronJobInterface::class);
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectRepository|EntityRepository|CronJobResultRepositoryInterface
     */
    protected function getCronJobResultRepository()
    {
        return $this->getRegistry()->getRepository(CronJobResultInterface::class);
    }

    /**
     * @return string
     */
    protected function getEnvironment()
    {
        if ($this->environment === null) {
            $this->environment = $this->kernel->getEnvironment();
        }

        return $this->environment;
    }
}
