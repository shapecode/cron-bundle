<?php

namespace Shapecode\Bundle\CronBundle\Repository\Interfaces;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\NonUniqueResultException;
use Shapecode\Bundle\CronBundle\Entity\Interfaces\CronJobInterface;
use Shapecode\Bundle\CronBundle\Entity\Interfaces\CronJobResultInterface;

/**
 * Interface CronJobResultRepositoryInterface
 *
 * @package Shapecode\Bundle\CronBundle\Repository\Interfaces
 * @author  Nikita Loges
 * @company tenolo GbR
 */
interface CronJobResultRepositoryInterface extends ObjectRepository
{

    /**
     * @param CronJobInterface $job
     * @return mixed
     */
    public function deleteOldLogs(CronJobInterface $job = null);

    /**
     * @param CronJobInterface $job
     * @return CronJobResultInterface
     * @throws NonUniqueResultException
     */
    public function findMostRecent(CronJobInterface $job = null);
}
