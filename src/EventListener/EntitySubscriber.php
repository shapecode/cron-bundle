<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\EventListener;

use DateTime;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use ReflectionClass;
use Shapecode\Bundle\CronBundle\Entity\AbstractEntity;
use function assert;

final class EntitySubscriber implements EventSubscriber
{
    /**
     * @inheritDoc
     */
    public function getSubscribedEvents() : array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    public function prePersist(LifecycleEventArgs $args) : void
    {
        $this->setDates($args);
    }

    public function preUpdate(LifecycleEventArgs $args) : void
    {
        $this->setDates($args);
    }

    private function setDates(LifecycleEventArgs $args) : void
    {
        $entity = $args->getObject();
        assert($entity instanceof AbstractEntity);
        $reflection = new ReflectionClass($entity);

        if (! $reflection->isSubclassOf(AbstractEntity::class)) {
            return;
        }

        $entity->setUpdatedAt(new DateTime());
    }
}
