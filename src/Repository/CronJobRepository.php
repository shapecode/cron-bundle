<?php

namespace Shapecode\Bundle\CronBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Shapecode\Bundle\CronBundle\Entity\Interfaces\CronJobInterface;
use Shapecode\Bundle\CronBundle\Repository\Interfaces\CronJobRepositoryInterface;

/**
 * Class CronJobRepository
 *
 * @package Shapecode\Bundle\CronBundle\Repository
 * @author  Nikita Loges
 */
class CronJobRepository extends EntityRepository implements CronJobRepositoryInterface
{

    /**
     * @inheritdoc
     */
    public function findOneByCommand($command, $number = 1)
    {
        return $this->findOneBy([
            'command' => $command,
            'number'  => $number
        ]);
    }

    /**
     * @inheritdoc
     */
    public function findByCommand($command)
    {
        return $this->findBy([
            'command' => $command
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getKnownJobs()
    {
        $data = new ArrayCollection($this->findAll());

        return $data->map(function (CronJobInterface $o) {
            return $o->getCommand();
        });
    }

    /**
     * @inheritdoc
     */
    public function findDueTasks()
    {
        $qb = $this->createQueryBuilder('p');
        $expr = $qb->expr();

        $qb->andWhere($expr->lte('p.nextRun', ':time'));
        $qb->andWhere($expr->eq('p.enable', ':enabled'));

        $qb->setParameter('time', new \DateTime());
        $qb->setParameter('enabled', true);

        return $qb->getQuery()->getResult();
    }
}
