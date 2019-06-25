<?php

namespace Shapecode\Bundle\CronBundle\EventListener;

use Shapecode\Bundle\CronBundle\Event\LoadJobsEvent;
use Shapecode\Bundle\CronBundle\Model\CronJobMetadata;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ServiceJobLoaderListener
 *
 * @package Shapecode\Bundle\CronBundle\EventListener
 * @author  Nikita Loges
 */
class ServiceJobLoaderListener implements EventSubscriberInterface
{

    /** @var CronJobMetadata[] */
    protected $jobs = [];

    /**
     * @param string   $expression
     * @param Command  $command
     * @param int|null $arguments
     * @param int $maxInstances
     */
    public function addCommand(string $expression, Command $command, ?int $arguments = null, int $maxInstances = 1)
    {
        $this->jobs[] = CronJobMetadata::createByCommand($expression, $command, $arguments, $maxInstances);
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            LoadJobsEvent::NAME => 'onLoadJobs'
        ];
    }

    /**
     * @param LoadJobsEvent $event
     */
    public function onLoadJobs(LoadJobsEvent $event): void
    {
        foreach ($this->jobs as $job) {
            $event->addJob($job);
        }
    }
}
