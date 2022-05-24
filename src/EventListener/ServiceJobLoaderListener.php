<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\EventListener;

use Shapecode\Bundle\CronBundle\Event\LoadJobsEvent;
use Shapecode\Bundle\CronBundle\Model\CronJobMetadata;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener()]
final class ServiceJobLoaderListener
{
    /** @var list<CronJobMetadata> */
    private array $jobs = [];

    public function __invoke(LoadJobsEvent $event): void
    {
        foreach ($this->jobs as $job) {
            $event->addJob($job);
        }
    }

    public function addCommand(
        string $expression,
        Command $command,
        ?string $arguments = null,
        int $maxInstances = 1
    ): void {
        $this->jobs[] = CronJobMetadata::createByCommand($expression, $command, $arguments, $maxInstances);
    }
}
