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
 * @company tenolo GbR
 */
class ServiceJobLoaderListener implements EventSubscriberInterface
{

    /** @var CronJobMetadata[] */
    protected $jobs = [];

    /**
     * @param         $expression
     * @param Command $command
     * @param null    $arguments
     */
    public function addCommand($expression, Command $command, $arguments = null)
    {
        $this->jobs[] = CronJobMetadata::createByCommand($expression, $command, $arguments);
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
    public function onLoadJobs(LoadJobsEvent $event)
    {
        foreach ($this->jobs as $job) {
            $event->addJob($job);
        }
    }
}
