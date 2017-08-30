<?php

namespace Shapecode\Bundle\CronBundle\Command;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\EntityRepository;
use Shapecode\Bundle\CronBundle\Entity\Interfaces\CronJobInterface;
use Shapecode\Bundle\CronBundle\Entity\Interfaces\CronJobResultInterface;
use Shapecode\Bundle\CronBundle\Repository\Interfaces\CronJobRepositoryInterface;
use Shapecode\Bundle\CronBundle\Repository\Interfaces\CronJobResultRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Class BaseCronjob
 *
 * @package Shapecode\Bundle\CronBundle\Command
 * @author  Nikita Loges
 */
abstract class BaseCommand extends ContainerAwareCommand
{

    /**
     * @return KernelInterface
     */
    protected function getKernel()
    {
        return $this->getContainer()->get('kernel');
    }

    /**
     * @return Reader
     */
    public function getReader()
    {
        return $this->getContainer()->get('annotation_reader');
    }

    /**
     * @return Registry
     */
    protected function getDoctrine()
    {
        return $this->getContainer()->get('doctrine');
    }

    /**
     * @return Stopwatch
     */
    protected function getStopWatch()
    {
        return $this->getContainer()->get('debug.stopwatch');
    }

    /**
     * @return null|\Symfony\Component\HttpFoundation\Request
     */
    protected function getRequest()
    {
        return $this->getContainer()->get('request_stack')->getCurrentRequest();
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
            return $this->getDoctrine()->getManager();
        }

        return $this->getDoctrine()->getManagerForClass($className);
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
     * @return \Doctrine\Common\Persistence\ObjectRepository|EntityRepository|CronJobRepositoryInterface
     */
    protected function getCronJobRepository()
    {
        return $this->findRepository(CronJobInterface::class);
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectRepository|EntityRepository|CronJobResultRepositoryInterface
     */
    protected function getCronJobResultRepository()
    {
        return $this->findRepository(CronJobResultInterface::class);
    }
}
