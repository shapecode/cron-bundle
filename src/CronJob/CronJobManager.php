<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\CronJob;

use Shapecode\Bundle\CronBundle\Collection\CronJobMetadataCollection;
use Shapecode\Bundle\CronBundle\Event\LoadJobsEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class CronJobManager
{
    private CronJobMetadataCollection $metadataCollection;

    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
        $this->metadataCollection = new CronJobMetadataCollection();
    }

    public function getJobs(): CronJobMetadataCollection
    {
        if ($this->metadataCollection->isEmpty()) {
            $this->eventDispatcher->dispatch(new LoadJobsEvent($this->metadataCollection));
        }

        return $this->metadataCollection;
    }
}
