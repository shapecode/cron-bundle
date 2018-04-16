<?php

namespace Shapecode\Bundle\CronBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectRepository;
use Shapecode\Bundle\CronBundle\Entity\CronJobInterface;

/**
 * Interface CronJobRepositoryInterface
 *
 * @package Shapecode\Bundle\CronBundle\Repository
 * @author  Nikita Loges
 */
interface CronJobRepositoryInterface extends ObjectRepository
{

    /**
     * @param     $command
     * @param int $number
     *
     * @return null|CronJobInterface
     */
    public function findOneByCommand($command, $number = 1);

    /**
     * @param $command
     *
     * @return array|CronJobInterface[]
     */
    public function findByCommand($command);

    /**
     * @return ArrayCollection|string[]
     */
    public function getKnownJobs();
}
