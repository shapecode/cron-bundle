<?php

namespace Shapecode\Bundle\CronBundle\Event;

use Shapecode\Bundle\CronBundle\Model\CronJobMetadata;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class LoadJobsEvent
 *
 * @package Shapecode\Bundle\CronBundle\Event
 * @author  Nikita Loges
 */
class LoadJobsEvent extends Event
{
    public const NAME = 'shapecode_cron.load_jobs';

    /** @var CronJobMetadata[]|array */
    protected $jobs = [];

    /**
     * @param CronJobMetadata $cronJobMetadata
     */
    public function addJob(CronJobMetadata $cronJobMetadata): void
    {
        $this->jobs[] = $cronJobMetadata;
    }

    /**
     * @return CronJobMetadata[]
     */
    public function getJobs(): array
    {
        return $this->jobs;
    }
}
