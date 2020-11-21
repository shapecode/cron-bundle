<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\EventListener;

use DateTime;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Shapecode\Bundle\CronBundle\Entity\AbstractEntity;

final class EntitySubscriber implements EventSubscriber
{
    /**
     * @inheritDoc
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $this->setDates($args);
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $this->setDates($args);
    }

    private function setDates(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (! $entity instanceof AbstractEntity) {
            return;
        }

        $entity->setUpdatedAt(new DateTime());
    }
}
