<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
    public function findByCommand(string $command): array
    {
        return $this->findBy(['command' => $command]);
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
