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
     * @inheritdoc
     */
    public function findOneByCommand($command, $number = 1)
    {
        return $this->findOneBy([
            'command' => $command,
            'number'  => $number
        ]);
    }

    /**
     * @inheritdoc
     */
    public function findByCommand($command)
    {
        return $this->findBy([
            'command' => $command
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getKnownJobs()
    {
        $data = new ArrayCollection($this->findAll());

        return $data->map(function (CronJobInterface $o) {
            return $o->getCommand();
        });
    }
}
