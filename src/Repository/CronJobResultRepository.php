<?php

namespace Shapecode\Bundle\CronBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Shapecode\Bundle\CronBundle\Entity\CronJobInterface;
use Shapecode\Bundle\CronBundle\Entity\CronJobResultInterface;

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
    public function deleteOldLogs(\DateTime $time)
    {
        $qb = $this->createQueryBuilder('d');
        $qb->delete($this->getEntityName(), 'd');
        $expr = $qb->expr();

        $qb->andWhere($expr->lte('d.createdAt', ':createdAt'));
        $qb->setParameter('createdAt', $time);

        return $qb->getQuery()->execute();
    }

    /**
     * @inheritdoc
     */
    public function findMostRecent(?CronJobInterface $job = null): ?CronJobResultInterface
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
