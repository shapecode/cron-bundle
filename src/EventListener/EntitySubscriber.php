<?php

namespace Shapecode\Bundle\CronBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Shapecode\Bundle\CronBundle\Entity\AbstractEntity;

/**
 * Class EntitySubscriber
 *
 * @package Shapecode\Bundle\CronBundle\EventListener
 * @author  Nikita Loges
 */
class EntitySubscriber implements EventSubscriber
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

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $this->setDates($args);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args): void
    {
        $this->setDates($args);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    protected function setDates(LifecycleEventArgs $args): void
    {
        /** @var AbstractEntity|object $entity */
        $entity = $args->getObject();
        $reflection = new \ReflectionClass($entity);

        if (!$reflection->isSubclassOf(AbstractEntity::class)) {
            return;
        }

        $entity->setUpdatedAt(new \DateTime());

        if ($entity->getCreatedAt() === null) {
            $entity->setCreatedAt(new \DateTime());
        }
    }

}
