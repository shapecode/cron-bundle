<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\EventListener;

use Shapecode\Bundle\CronBundle\Event\LoadJobsEvent;
use Shapecode\Bundle\CronBundle\Model\CronJobMetadata;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ServiceJobLoaderListener implements EventSubscriberInterface
{
    /** @var CronJobMetadata[] */
    private $jobs = [];

    public function addCommand(
        string $expression,
        Command $command,
        ?string $arguments = null,
        int $maxInstances = 1
    ) : void {
        $this->jobs[] = CronJobMetadata::createByCommand($expression, $command, $arguments, $maxInstances);
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents() : array
    {
        return [
            LoadJobsEvent::NAME => 'onLoadJobs',
        ];
    }

    public function onLoadJobs(LoadJobsEvent $event) : void
    {
        foreach ($this->jobs as $job) {
            $event->addJob($job);
        }
    }
}
