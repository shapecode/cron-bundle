<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Tests\EventListener;

use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Clock\ClockInterface;
use Shapecode\Bundle\CronBundle\Entity\AbstractEntity;
use Shapecode\Bundle\CronBundle\EventListener\EntitySubscriber;
use stdClass;

class EntitySubscriberTest extends TestCase
{
    private ClockInterface & MockObject $clock;

    private EntitySubscriber $subscriber;

    protected function setUp(): void
    {
        $this->clock      = $this->createMock(ClockInterface::class);
        $this->subscriber = new EntitySubscriber($this->clock);
    }

    public function testPrePersistSetsCreatedAtAndUpdatedAtWhenNull(): void
    {
        $now = new DateTimeImmutable('2024-10-10 12:00:00');
        $this->clock->method('now')->willReturn($now);

        $entity = $this->createMock(AbstractEntity::class);

        $entity->expects(self::once())
            ->method('setCreatedAt')
            ->with(self::isInstanceOf(DateTime::class));

        $entity->expects(self::once())
            ->method('setUpdatedAt')
            ->with(self::isInstanceOf(DateTime::class));

        $entity->method('getCreatedAt')->willReturn(null);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $args          = new LifecycleEventArgs($entity, $entityManager);

        $this->subscriber->prePersist($args);
    }

    public function testPreUpdateSetsUpdatedAt(): void
    {
        $now = new DateTimeImmutable('2024-10-10 12:00:00');
        $this->clock->method('now')->willReturn($now);

        $entity = $this->createMock(AbstractEntity::class);

        $entity->expects(self::never())
            ->method('setCreatedAt');

        $entity->expects(self::once())
            ->method('setUpdatedAt')
            ->with(self::isInstanceOf(DateTime::class));

        $entity->method('getCreatedAt')->willReturn(new DateTime());

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $args          = new LifecycleEventArgs($entity, $entityManager);

        $this->subscriber->preUpdate($args);
    }

    public function testEntityNotInstanceOfAbstractEntity(): void
    {
        $nonEntity = new stdClass();

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $args          = new LifecycleEventArgs($nonEntity, $entityManager);

        $this->subscriber->prePersist($args);
        $this->subscriber->preUpdate($args);

        $this->expectNotToPerformAssertions();
    }
}
