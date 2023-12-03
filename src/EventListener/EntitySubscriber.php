<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\EventListener;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Psr\Clock\ClockInterface;
use Shapecode\Bundle\CronBundle\Entity\AbstractEntity;

#[AsDoctrineListener(Events::prePersist)]
#[AsDoctrineListener(Events::preUpdate)]
final class EntitySubscriber
{
    public function __construct(
        private readonly ClockInterface $clock,
    ) {
    }

    /** @param LifecycleEventArgs<EntityManagerInterface> $args */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $this->setDates($args);
    }

    /** @param LifecycleEventArgs<EntityManagerInterface> $args */
    public function preUpdate(LifecycleEventArgs $args): void
    {
        $this->setDates($args);
    }

    /** @param LifecycleEventArgs<EntityManagerInterface> $args */
    private function setDates(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (! $entity instanceof AbstractEntity) {
            return;
        }

        $now = $this->clock->now();

        if ($entity->getCreatedAt() === null) {
            $entity->setCreatedAt($now);
        }

        $entity->setUpdatedAt($now);
    }
}
