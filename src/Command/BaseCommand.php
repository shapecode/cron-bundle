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
        if (is_null($this->stopwatch)) {
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
     * @param null $className
     *
     * @return \Doctrine\Common\Persistence\ObjectManager|null
     */
    protected function getEntityManager($className = null)
    {
        if (is_object($className)) {
            $className = get_class($className);
        }

        if (is_null($className)) {
            return $this->getRegistry()->getManager();
        }

        return $this->getRegistry()->getManagerForClass($className);
    }

    /**
     * @param $className
     *
     * @return \Doctrine\Common\Persistence\ObjectRepository|EntityRepository
     */
    protected function findRepository($className)
    {
        return $this->getRegistry()->getRepository($className);
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectRepository|EntityRepository|CronJobRepositoryInterface
     */
    protected function getCronJobRepository()
    {
        $em = $this->getEntityManager(CronJobInterface::class);

        return $em->getRepository(CronJobInterface::class);
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectRepository|EntityRepository|CronJobResultRepositoryInterface
     */
    protected function getCronJobResultRepository()
    {
        $em = $this->getEntityManager(CronJobResultInterface::class);

        return $em->getRepository(CronJobResultInterface::class);
    }
}
