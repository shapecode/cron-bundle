<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\Persistence\ManagerRegistry;
use Shapecode\Bundle\CronBundle\Collection\CronJobCollection;
use Shapecode\Bundle\CronBundle\Entity\CronJob;

/** @extends ServiceEntityRepository<CronJob> */
class CronJobRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CronJob::class);
    }

    public function findOneByCommand(string $command, int $number = 1): CronJob|null
    {
        return $this->findOneBy([
            'command' => $command,
            'number'  => $number,
        ]);
    }

    public function findAllCollection(): CronJobCollection
    {
        return new CronJobCollection(...$this->findAll());
    }

    public function findByCommandOrId(string $commandOrId): CronJobCollection
    {
        $qb = $this->createQueryBuilder('p');

        /** @var list<CronJob> $result */
        $result = $qb
            ->andWhere(
                $qb->expr()->orX(
                    'p.command = :command',
                    'p.id = :command',
                ),
            )
            ->setParameter('command', $commandOrId, Types::STRING)
            ->getQuery()
            ->getResult();

        return new CronJobCollection(...$result);
    }
}
