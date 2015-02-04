<?php

namespace Shapecode\Bundle\CronBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Shapecode\Bundle\CronBundle\Entity\CronJob;

/**
 * Class CronJobRepository
 * @package Shapecode\Bundle\CronBundle\Repository
 * @author Nikita Loges
 */
class CronJobRepository extends EntityRepository
{

    /**
     * @param $command
     * @return null|CronJob
     */
    public function findOneByCommand($command)
    {
        return $this->findOneBy(array('command' => $command));
    }

    /**
     * @return \Doctrine\Common\Collections\Collection|static
     */
    public function getKnownJobs()
    {
        $data = new ArrayCollection($this->findAll());

        return $data->map(function ($o) {
            /** @var CronJob $o */
            return $o->getCommand();
        });
    }

    /**
     * @return array
     */
    public function findDueTasks()
    {
        $qb = $this->createQueryBuilder('p');
        $expr = $qb->expr();
        $qb->andWhere($expr->lte('p.nextRun', ':time'));
        $qb->andWhere($expr->lte('p.isEnable', ':enabled'));

        $qb->setParameter('time', new \DateTime());
        $qb->setParameter('enabled', true);

        return $qb->getQuery()->getResult();
    }
}
