<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Manager;

use RuntimeException;
use Shapecode\Bundle\CronBundle\Event\LoadJobsEvent;
use Shapecode\Bundle\CronBundle\Model\CronJobMetadata;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\LegacyEventDispatcherProxy;
use Symfony\Component\HttpKernel\Kernel;

class CronJobManager implements CronJobManagerInterface
{
    /** @var CronJobMetadata[] */
    protected $jobs;

    /** @var LegacyEventDispatcherProxy|EventDispatcherInterface */
    protected $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        if (Kernel::VERSION_ID > 40300) {
            $legacy = LegacyEventDispatcherProxy::decorate($eventDispatcher);

            if ($legacy === null) {
                throw new RuntimeException('there is not event dispatcher provided');
            }

            $eventDispatcher = $legacy;
        }

        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @return CronJobMetadata[]
     */
    protected function initJobs() : array
    {
        $event = new LoadJobsEvent();

        $this->eventDispatcher->dispatch(LoadJobsEvent::NAME, $event);

        return $event->getJobs();
    }

    /**
     * @inheritdoc
     */
    public function getJobs() : array
    {
        if ($this->jobs === null) {
            $this->jobs = $this->initJobs();
        }

        return $this->jobs;
    }
}
