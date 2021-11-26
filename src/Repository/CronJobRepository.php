<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityRepository;
use Shapecode\Bundle\CronBundle\Entity\CronJob;

/**
 * @extends EntityRepository<CronJob>
 */
class CronJobRepository extends EntityRepository
{
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
     * @return Collection<int, string>
     */
    public function getKnownJobs(): Collection
    {
        $data = new ArrayCollection($this->findAll());

        return $data->map(static function (CronJob $o): string {
            return $o->getCommand();
        });
    }
}
