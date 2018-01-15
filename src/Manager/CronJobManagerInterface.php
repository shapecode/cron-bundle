<?php

namespace Shapecode\Bundle\CronBundle\Manager;

use Shapecode\Bundle\CronBundle\Model\CronJobMetadata;
use Symfony\Component\Console\Command\Command;

/**
 * Interface CronJobManagerInterface
 *
 * @package Shapecode\Bundle\CronBundle\Manager
 * @author  Nikita Loges
 */
interface CronJobManagerInterface
{

    /**
     * @return mixed
     */
    public function getApplicationJobs();

    /**
     * @return array|CronJobMetadata[]
     */
    public function getJobs();

    /**
     * @param Command $command
     * @param         $expression
     */
    public function addJob(Command $command, $expression);

}
