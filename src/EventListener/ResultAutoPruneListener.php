<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\EventListener;

use Shapecode\Bundle\CronBundle\Event\GenericCleanUpEvent;
use Shapecode\Bundle\CronBundle\Service\CronJobResultServiceInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ResultAutoPruneListener implements EventSubscriberInterface
{
    /** @var CronJobResultServiceInterface */
    protected $cronjobService;

    /** @var bool */
    protected $autoPrune;

    public function __construct(CronJobResultServiceInterface $cronjobService, bool $autoPrune)
    {
        $this->cronjobService = $cronjobService;
        $this->autoPrune      = $autoPrune;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents() : array
    {
        return [
            GenericCleanUpEvent::HOURLY_PROCESS => 'onHourlyProcess',
        ];
    }

    public function onHourlyProcess(GenericCleanUpEvent $genericCleanUpEvent) : void
    {
        if (! $this->autoPrune) {
            return;
        }

        $this->cronjobService->prune();
    }
}
