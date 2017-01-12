<?php

namespace Shapecode\Bundle\CronBundle\Command;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Annotations\Reader;
use Shapecode\Bundle\CronBundle\Entity\Interfaces\CronJobInterface;
use Shapecode\Bundle\CronBundle\Entity\Interfaces\CronJobResultInterface;
use Shapecode\Bundle\CronBundle\Repository\Interfaces\CronJobRepositoryInterface;
use Shapecode\Bundle\CronBundle\Repository\Interfaces\CronJobResultRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class BaseCronjob
 *
 * @package Shapecode\Bundle\CronBundle\Command
 * @author  Nikita Loges
 * @date    02.02.2015
 */
abstract class BaseCommand extends ContainerAwareCommand
{

    /** @var string */
    protected $commandName = '';

    /** @var string */
    protected $commandDescription = '';

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName($this->commandName);
        $this->setDescription($this->commandDescription);
    }

    /**
     * @inheritdoc
     */
    protected function getContainer()
    {
        return parent::getContainer();
    }

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
     * @return \Doctrine\Common\Persistence\ObjectManager|object
     */
    protected function getEntityManager()
    {
        return $this->getDoctrine()->getManager();
    }

    /**
     * @return null|\Symfony\Component\HttpFoundation\Request
     */
    protected function getRequest()
    {
        return $this->getContainer()->get('request_stack')->getCurrentRequest();
    }

    /**
     * @return CronJobRepositoryInterface
     */
    protected function getCronJobRepository()
    {
        return $this->getEntityManager()->getRepository(CronJobInterface::class);
    }

    /**
     * @return CronJobResultRepositoryInterface
     */
    protected function getCronJobResultRepository()
    {
        return $this->getEntityManager()->getRepository(CronJobResultInterface::class);
    }
}
