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

            // deprecated, use class name instead
            // @phpstan-ignore-next-line
            $this->eventDispatcher->dispatch($event, LoadJobsEvent::NAME);

            $this->eventDispatcher->dispatch($event);

            $this->jobs = $event->getJobs();
        }

        return $this->jobs;
    }
}
