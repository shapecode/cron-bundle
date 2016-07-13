<?php

namespace Shapecode\Bundle\CronBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Shapecode\Bundle\CronBundle\Entity\CronJob;
use Shapecode\Bundle\CronBundle\Entity\CronJobResult;

/**
 * Class CronJobResultRepository
 * @package Shapecode\Bundle\CronBundle\Repository
 * @author Nikita Loges
 */
class CronJobResultRepository extends EntityRepository
{

    /**
     * @param CronJob $job
     * @return mixed
     */
    public function deleteOldLogs(CronJob $job = null)
    {
        $qb = $this->createQueryBuilder('d');
        $qb->delete($this->getEntityName(), 'd');
        $expr = $qb->expr();

        $qb->andWhere($expr->lte('d.createdAt', ':createdAt'));
        $qb->setParameter('createdAt', new \DateTime('7 days ago'));

        if ($job) {
            $qb->andWhere($expr->eq('r.cronJob', ':cronJob'));
            $qb->setParameter('cronJob', $job->getId());
        }

        return $qb->getQuery()->execute();
    }

    /**
     * @param CronJob $job
     * @return CronJobResult
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findMostRecent(CronJob $job = null)
    {
        $qb = $this->createQueryBuilder('p');
        $expr = $qb->expr();

        if ($job) {
            $qb->andWhere($expr->eq('p.cronJob', ':cronJob'));
            $qb->setParameter('cronJob', $job->getId());
        }

        $qb->orderBy('p.createdAt', 'DESC');
        $qb->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();

    }
}
