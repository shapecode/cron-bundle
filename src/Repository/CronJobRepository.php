<?php

namespace Shapecode\Bundle\CronBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Shapecode\Bundle\CronBundle\Entity\CronJobInterface;

/**
 * Class CronJobRepository
 *
 * @package Shapecode\Bundle\CronBundle\Repository
 * @author  Nikita Loges
 */
class CronJobRepository extends EntityRepository implements CronJobRepositoryInterface
{

    /**
     * @param     $command
     * @param int $number
     *
     * @return CronJobInterface|null
     */
    public function findOneByCommand(string $command, int $number = 1): ?CronJobInterface
    {
        return $this->findOneBy([
            'command' => $command,
            'number'  => $number,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function findByCommand(string $command): array
    {
        return $this->findBy([
            'command' => $command,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getKnownJobs(): ArrayCollection
    {
        $data = new ArrayCollection($this->findAll());

        return $data->map(function (CronJobInterface $o) {
            return $o->getCommand();
        });
    }
}
