<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Repository;

use DateTimeInterface;
use Doctrine\ORM\EntityRepository;
use Shapecode\Bundle\CronBundle\Entity\CronJobResult;

/**
 * @extends EntityRepository<CronJobResult>
 */
class CronJobResultRepository extends EntityRepository
{
    public function deleteOldLogs(DateTimeInterface $time): void
    {
        $this->_em->createQuery(
            <<<'DQL'
                DELETE FROM Shapecode\Bundle\CronBundle\Entity\CronJobResult d
                WHERE d.createdAt <= :createdAt
            DQL
        )
            ->setParameter('createdAt', $time)
            ->execute();
    }
}
