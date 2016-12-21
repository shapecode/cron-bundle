<?php

namespace Shapecode\Bundle\CronBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Shapecode\Bundle\CronBundle\Entity\Interfaces\CronJobInterface;
use Shapecode\Bundle\CronBundle\Repository\Interfaces\CronJobResultRepositoryInterface;

/**
 * Class CronJobResultRepository
 *
 * @package Shapecode\Bundle\CronBundle\Repository
 * @author  Nikita Loges
 */
class CronJobResultRepository extends EntityRepository implements CronJobResultRepositoryInterface
{

    /**
     * @inheritdoc
     */
    public function deleteOldLogs(CronJobInterface $job = null)
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
     * @inheritdoc
     */
    public function findMostRecent(CronJobInterface $job = null)
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
