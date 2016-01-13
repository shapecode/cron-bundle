<?php

namespace Shapecode\Bundle\CronBundle\Command;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Annotations\Reader;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class BaseCronjob
 * @package Shapecode\Bundle\CronBundle\Command
 * @author Nikita Loges
 * @date 02.02.2015
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
     * @param $id
     * @return object
     */
    protected function get($id)
    {
        return $this->getContainer()->get($id);
    }

    /**
     * @return KernelInterface
     */
    protected function getKernel()
    {
        return $this->get('kernel');
    }

    /**
     * @return Reader
     */
    public function getReader()
    {
        return $this->get('annotation_reader');
    }

    /**
     * @return Registry
     */
    protected function getDoctrine()
    {
        return $this->get('doctrine');
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
        return $this->get('request_stack')->getCurrentRequest();
    }

    /**
     * @return \Shapecode\Bundle\CronBundle\Repository\CronJobRepository
     */
    protected function getCronJobRepository()
    {
        return $this->getEntityManager()->getRepository('ShapecodeCronBundle:CronJob');
    }

    /**
     * @return \Shapecode\Bundle\CronBundle\Repository\CronJobResultRepository
     */
    protected function getCronJobResultRepository()
    {
        return $this->getEntityManager()->getRepository('ShapecodeCronBundle:CronJobResult');
    }
}
