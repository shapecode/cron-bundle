<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Repository;

use DateTime;
use Doctrine\ORM\EntityRepository;
use Shapecode\Bundle\CronBundle\Entity\CronJobInterface;
use Shapecode\Bundle\CronBundle\Entity\CronJobResultInterface;

class CronJobResultRepository extends EntityRepository implements CronJobResultRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function deleteOldLogs(DateTime $time)
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
    public function findMostRecent(?CronJobInterface $job = null) : ?CronJobResultInterface
    {
        $qb   = $this->createQueryBuilder('p');
        $expr = $qb->expr();

        if ($job !== null) {
            $qb->andWhere($expr->eq('p.cronJob', ':cronJob'));
            $qb->setParameter('cronJob', $job->getId());
        }

        $qb->orderBy('p.createdAt', 'DESC');
        $qb->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
