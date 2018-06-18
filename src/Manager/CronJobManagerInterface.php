<?php

namespace Shapecode\Bundle\CronBundle\Manager;

use Shapecode\Bundle\CronBundle\Model\CronJobMetadata;

/**
 * Interface CronJobManagerInterface
 *
 * @package Shapecode\Bundle\CronBundle\Manager
 * @author  Nikita Loges
 */
interface CronJobManagerInterface
{

    /**
     * @return CronJobMetadata[]
     */
    public function getJobs();

}
