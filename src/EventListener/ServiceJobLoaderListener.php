<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\EventListener;

use Shapecode\Bundle\CronBundle\Collection\CronJobMetadataCollection;
use Shapecode\Bundle\CronBundle\Domain\CronJobMetadata;
use Shapecode\Bundle\CronBundle\Event\LoadJobsEvent;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
final class ServiceJobLoaderListener
{
    private readonly CronJobMetadataCollection $metadataCollection;

    public function __construct()
    {
        $this->metadataCollection = new CronJobMetadataCollection();
    }

    public function __invoke(LoadJobsEvent $event): void
    {
        foreach ($this->metadataCollection as $job) {
            $event->addJob($job);
        }
    }

    public function addCommand(
        string $expression,
        Command $command,
        ?string $arguments = null,
        int $maxInstances = 1
    ): void {
        $this->metadataCollection->add(
            CronJobMetadata::createByCommand($expression, $command, $arguments, $maxInstances)
        );
    }
}
