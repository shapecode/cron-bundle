<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Tests\Annotation;

use Doctrine\Common\Annotations\AnnotationReader;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Shapecode\Bundle\CronBundle\Annotation\CronJob;
use Shapecode\Bundle\CronBundle\Tests\Annotation\data\TestModel;

final class CronJobTest extends TestCase
{
    public function testCreation() : void
    {
        $annotation = new CronJob([
            'value' => 'pwd',
        ]);

        self::assertEquals('pwd', $annotation->value);
        self::assertNull($annotation->arguments);
        self::assertEquals(1, $annotation->maxInstances);

        $annotation = new CronJob([
            'value'        => 'ls',
            'arguments'    => '-l',
            'maxInstances' => 5,
        ]);

        self::assertEquals('ls', $annotation->value);
        self::assertEquals('-l', $annotation->arguments);
        self::assertEquals(5, $annotation->maxInstances);
    }

    public function testReader() : void
    {
        $reader = new AnnotationReader();

        $ref        = new ReflectionClass(TestModel::class);
        $annotation = $reader->getClassAnnotation($ref, CronJob::class);

        self::assertEquals('ls', $annotation->value);
        self::assertEquals('-l', $annotation->arguments);
        self::assertEquals(3, $annotation->maxInstances);
    }
}
