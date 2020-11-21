<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityRepository;
use Shapecode\Bundle\CronBundle\Entity\CronJobInterface;

use function assert;

class CronJobRepository extends EntityRepository implements CronJobRepositoryInterface
{
    public function findOneByCommand(string $command, int $number = 1): ?CronJobInterface
    {
        $object = $this->findOneBy([
            'command' => $command,
            'number'  => $number,
        ]);
        assert($object instanceof CronJobInterface || $object === null);

        return $object;
    }

    /**
     * @inheritDoc
     */
    public function findByCommand(string $command): array
    {
        return $this->findBy([
            'command' => $command,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getKnownJobs(): Collection
    {
        $data = new ArrayCollection($this->findAll());

        return $data->map(static function (CronJobInterface $o): string {
            return $o->getCommand();
        });
    }
}
