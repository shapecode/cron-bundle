<?php

namespace Shapecode\Bundle\CronBundle\EventListener;

use Shapecode\Bundle\CronBundle\Event\GenericCleanUpEvent;
use Shapecode\Bundle\CronBundle\Service\CronJobResultServiceInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ResultAutoPruneListener
 *
 * @package Shapecode\Bundle\CronBundle\EventListener
 * @author  Nikita Loges
 */
class ResultAutoPruneListener implements EventSubscriberInterface
{

    /** @var CronJobResultServiceInterface */
    protected $cronjobService;

    /** @var boolean */
    protected $autoPrune;

    /**
     * @param CronJobResultServiceInterface $cronjobService
     * @param bool                          $autoPrune
     */
    public function __construct(CronJobResultServiceInterface $cronjobService, bool $autoPrune)
    {
        $this->cronjobService = $cronjobService;
        $this->autoPrune = $autoPrune;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            GenericCleanUpEvent::HOURLY_PROCESS => 'onHourlyProcess'
        ];
    }

    /**
     * @param GenericCleanUpEvent $genericCleanUpEvent
     */
    public function onHourlyProcess(GenericCleanUpEvent $genericCleanUpEvent): void
    {
        if ($this->autoPrune) {
            $this->cronjobService->prune();
        }
    }

}
