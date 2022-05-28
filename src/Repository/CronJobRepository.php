<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\Persistence\ManagerRegistry;
use Shapecode\Bundle\CronBundle\Entity\CronJob;

use function array_map;

/**
 * @extends ServiceEntityRepository<CronJob>
 */
class CronJobRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CronJob::class);
    }

    public function findOneByCommand(string $command, int $number = 1): ?CronJob
    {
        return $this->findOneBy([
            'command' => $command,
            'number'  => $number,
        ]);
    }

    /**
     * @return list<CronJob>
     */
    public function findByCommandOrId(string $commandOrId): array
    {
        $qb = $this->createQueryBuilder('p');

        return $qb
            ->andWhere(
                $qb->expr()->orX(
                    'p.command = :command',
                    'p.id= :command',
                )
            )
            ->setParameter('command', $commandOrId, Types::STRING)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return list<string>
     */
    public function getKnownJobs(): array
    {
        return array_map(
            static function (CronJob $o): string {
                return $o->getCommand();
            },
            $this->findAll()
        );
    }
}
