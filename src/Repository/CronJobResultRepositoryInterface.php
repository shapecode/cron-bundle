<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Repository;

use DateTime;
use Doctrine\Persistence\ObjectRepository;
use Shapecode\Bundle\CronBundle\Entity\CronJobInterface;
use Shapecode\Bundle\CronBundle\Entity\CronJobResultInterface;

/**
 * Interface CronJobResultRepositoryInterface
 */
interface CronJobResultRepositoryInterface extends ObjectRepository
{
    public function deleteOldLogs(DateTime $time) : void;

    public function findMostRecent(?CronJobInterface $job = null) : ?CronJobResultInterface;
}
