<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Event;

use Shapecode\Bundle\CronBundle\Model\CronJobMetadata;
use Symfony\Contracts\EventDispatcher\Event;

class LoadJobsEvent extends Event
{
    public const NAME = 'shapecode_cron.load_jobs';

    /** @var CronJobMetadata[]|array */
    protected $jobs = [];

    public function addJob(CronJobMetadata $cronJobMetadata) : void
    {
        $this->jobs[] = $cronJobMetadata;
    }

    /**
     * @return CronJobMetadata[]
     */
    public function getJobs() : array
    {
        return $this->jobs;
    }
}
