<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Repository;

use DateTime;
use Doctrine\ORM\EntityRepository;
use Shapecode\Bundle\CronBundle\Entity\CronJobResult;

/**
 * @method CronJobResult|null find($id, ?int $lockMode = null, ?int $lockVersion = null)
 * @method CronJobResult[] findAll()
 * @method CronJobResult|null findOneBy(array $criteria, array $orderBy = null)
 * @method CronJobResult[] findBy(array $criteria, array $orderBy = null, ?int $limit = null, ?int $offset = null)
 * @extends EntityRepository<CronJobResult>
 */
class CronJobResultRepository extends EntityRepository
{
    public function deleteOldLogs(DateTime $time): void
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
