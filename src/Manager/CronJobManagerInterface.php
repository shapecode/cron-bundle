<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Manager;

use Shapecode\Bundle\CronBundle\Model\CronJobMetadata;

/**
 * Interface CronJobManagerInterface
 */
interface CronJobManagerInterface
{
    /**
     * @return CronJobMetadata[]
     */
    public function getJobs() : array;
}
