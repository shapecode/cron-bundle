<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\EventListener;

use Shapecode\Bundle\CronBundle\Event\GenericCleanUpEvent;
use Shapecode\Bundle\CronBundle\Service\CronJobResultServiceInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ResultAutoPruneListener implements EventSubscriberInterface
{
    /** @var CronJobResultServiceInterface */
    private $cronjobService;

    /** @var bool */
    private $autoPrune;

    public function __construct(CronJobResultServiceInterface $cronjobService, bool $autoPrune)
    {
        $this->cronjobService = $cronjobService;
        $this->autoPrune      = $autoPrune;
    }

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
