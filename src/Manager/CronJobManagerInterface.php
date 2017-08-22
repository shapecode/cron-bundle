<?php

namespace Shapecode\Bundle\CronBundle\Manager;

use Shapecode\Bundle\CronBundle\Model\CronJobMetadata;

/**
 * Interface CronJobManagerInterface
 *
 * @package Shapecode\Bundle\CronBundle\Manager
 * @author  Nikita Loges
 * @company tenolo GbR
 */
interface CronJobManagerInterface
{

    /**
     * @return array|CronJobMetadata[]
     */
    public function getJobs();

}
