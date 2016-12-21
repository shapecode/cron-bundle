<?php

namespace Shapecode\Bundle\CronBundle\Repository\Interfaces;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectRepository;
use Shapecode\Bundle\CronBundle\Entity\Interfaces\CronJobInterface;

/**
 * Interface CronJobRepositoryInterface
 *
 * @package Shapecode\Bundle\CronBundle\Repository\Interfaces
 * @author  Nikita Loges
 * @company tenolo GbR
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

    /**
     * @return array
     */
    public function findDueTasks();
}
