<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Manager;

use Shapecode\Bundle\CronBundle\Event\LoadJobsEvent;
use Shapecode\Bundle\CronBundle\Model\CronJobMetadata;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class CronJobManager
{
    /** @var list<CronJobMetadata>|null */
    private ?array $jobs = null;

    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
    }

    /**
     * @return list<CronJobMetadata>
     */
    public function getJobs(): array
    {
        if ($this->jobs === null) {
            $event = new LoadJobsEvent();

            $this->eventDispatcher->dispatch($event);

            $this->jobs = $event->getJobs();
        }

        return $this->jobs;
    }
}
