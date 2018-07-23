<?php

namespace Shapecode\Bundle\CronBundle\Manager;

use Shapecode\Bundle\CronBundle\Event\LoadJobsEvent;
use Shapecode\Bundle\CronBundle\Model\CronJobMetadata;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class CronJobManager
 *
 * @package Shapecode\Bundle\CronBundle\Manager
 * @author  Nikita Loges
 */
class CronJobManager implements CronJobManagerInterface
{

    /** @var CronJobMetadata[] */
    protected $jobs;

    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @return CronJobMetadata[]
     */
    protected function initJobs(): array
    {
        $event = new LoadJobsEvent();
        $this->eventDispatcher->dispatch(LoadJobsEvent::NAME, $event);

        return $event->getJobs();
    }

    /**
     * @inheritdoc
     */
    public function getJobs(): array
    {
        if ($this->jobs === null) {
            $this->jobs = $this->initJobs();
        }

        return $this->jobs;
    }
}
