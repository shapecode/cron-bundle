<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Event;

use Shapecode\Bundle\CronBundle\Collection\CronJobMetadataCollection;
use Shapecode\Bundle\CronBundle\Domain\CronJobMetadata;
use Symfony\Contracts\EventDispatcher\Event;

final class LoadJobsEvent extends Event
{
    public function __construct(
        private readonly CronJobMetadataCollection $metadataCollection,
    ) {
    }

    public function addJob(CronJobMetadata $cronJobMetadata): void
    {
        $this->metadataCollection->add($cronJobMetadata);
    }
}
