<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 */
final class CronJob extends Annotation
{
    /** @var string|null */
    public $arguments;

    /** @var int */
    public $maxInstances = 1;
}
