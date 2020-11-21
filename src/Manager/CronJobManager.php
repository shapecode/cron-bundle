<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Manager;

use Shapecode\Bundle\CronBundle\Event\LoadJobsEvent;
use Shapecode\Bundle\CronBundle\Model\CronJobMetadata;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class CronJobManager
{
    /** @var CronJobMetadata[]|null */
    private ?array $jobs = null;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @return CronJobMetadata[]
     */
    private function initJobs(): array
    {
        $event = new LoadJobsEvent();

        $this->eventDispatcher->dispatch($event, LoadJobsEvent::NAME);

        return $event->getJobs();
    }

    /**
     * @return CronJobMetadata[]
     */
    public function getJobs(): array
    {
        if ($this->jobs === null) {
            $this->jobs = $this->initJobs();
        }

        return $this->jobs;
    }
}
