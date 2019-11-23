<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Command;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use RuntimeException;
use Shapecode\Bundle\CronBundle\Entity\CronJobInterface;
use Shapecode\Bundle\CronBundle\Entity\CronJobResultInterface;
use Shapecode\Bundle\CronBundle\Repository\CronJobRepositoryInterface;
use Shapecode\Bundle\CronBundle\Repository\CronJobResultRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Stopwatch\Stopwatch;

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

    public function __construct(
        KernelInterface $kernel,
        Reader $annotationReader,
        ManagerRegistry $registry,
        RequestStack $requestStack
    ) {
        parent::__construct();

        $this->kernel           = $kernel;
        $this->annotationReader = $annotationReader;
        $this->registry         = $registry;
        $this->requestStack     = $requestStack;
    }

    protected function getKernel() : KernelInterface
    {
        return $this->kernel;
    }

    public function getReader() : Reader
    {
        return $this->annotationReader;
    }

    protected function getRegistry() : ManagerRegistry
    {
        return $this->registry;
    }

    protected function getStopWatch() : Stopwatch
    {
        if ($this->stopwatch === null) {
            $this->stopwatch = new Stopwatch();
        }

        return $this->stopwatch;
    }

    protected function getRequest() : Request
    {
        $request = $this->requestStack->getMasterRequest();

        if ($request === null) {
            throw new RuntimeException('no master request there');
        }

        return $request;
    }

    protected function getManager() : ObjectManager
    {
        return $this->getRegistry()->getManager();
    }

    protected function getCronJobRepository() : CronJobRepositoryInterface
    {
        /** @var CronJobRepositoryInterface $repo */
        $repo = $this->getRegistry()->getRepository(CronJobInterface::class);

        return $repo;
    }

    protected function getCronJobResultRepository() : CronJobResultRepositoryInterface
    {
        /** @var CronJobResultRepositoryInterface $repo */
        $repo = $this->getRegistry()->getRepository(CronJobResultInterface::class);

        return $repo;
    }

    protected function getEnvironment() : string
    {
        if ($this->environment === null) {
            $this->environment = $this->kernel->getEnvironment();
        }

        return $this->environment;
    }
}
