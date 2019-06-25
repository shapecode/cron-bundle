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
     * @param string $command
     * @param int    $number
     *
     * @return null|CronJobInterface
     */
    public function findOneByCommand(string $command, int $number = 1): ?CronJobInterface;

    /**
     * @param string $command
     *
     * @return array|CronJobInterface[]
     */
    public function findByCommand(string $command): array;

    /**
     * @return ArrayCollection|string[]
     */
    public function getKnownJobs(): ArrayCollection;
}
