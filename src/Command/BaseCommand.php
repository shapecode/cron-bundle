<?php

namespace Shapecode\Bundle\CronBundle\Command;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Shapecode\Bundle\CronBundle\Entity\CronJobInterface;
use Shapecode\Bundle\CronBundle\Entity\CronJobResultInterface;
use Shapecode\Bundle\CronBundle\Repository\CronJobRepositoryInterface;
use Shapecode\Bundle\CronBundle\Repository\CronJobResultRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\HttpFoundation\Request;
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
    public function __construct(
        KernelInterface $kernel,
        Reader $annotationReader,
        ManagerRegistry $registry,
        RequestStack $requestStack
    ) {
        parent::__construct();

        $this->kernel = $kernel;
        $this->annotationReader = $annotationReader;
        $this->registry = $registry;
        $this->requestStack = $requestStack;
    }

    /**
     * @return KernelInterface
     */
    protected function getKernel(): KernelInterface
    {
        return $this->kernel;
    }

    /**
     * @return Reader
     */
    public function getReader(): Reader
    {
        return $this->annotationReader;
    }

    /**
     * @return ManagerRegistry
     */
    protected function getRegistry(): ManagerRegistry
    {
        return $this->registry;
    }

    /**
     * @return Stopwatch
     */
    protected function getStopWatch(): Stopwatch
    {
        if ($this->stopwatch === null) {
            $this->stopwatch = new Stopwatch();
        }

        return $this->stopwatch;
    }

    /**
     * @return Request
     */
    protected function getRequest(): Request
    {
        return $this->requestStack->getMasterRequest();
    }

    /**
     * @return ObjectManager
     */
    protected function getManager(): ObjectManager
    {
        return $this->getRegistry()->getManager();
    }

    /**
     * @return CronJobRepositoryInterface
     */
    protected function getCronJobRepository(): CronJobRepositoryInterface
    {
        return $this->getRegistry()->getRepository(CronJobInterface::class);
    }

    /**
     * @return CronJobResultRepositoryInterface
     */
    protected function getCronJobResultRepository(): CronJobResultRepositoryInterface
    {
        return $this->getRegistry()->getRepository(CronJobResultInterface::class);
    }

    /**
     * @return string
     */
    protected function getEnvironment(): string
    {
        if ($this->environment === null) {
            $this->environment = $this->kernel->getEnvironment();
        }

        return $this->environment;
    }
}
