<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @deprecated use AsCronJob attribute instead
 *
 * @Annotation
 */
final class CronJob extends Annotation
{
    public ?string $arguments = null;

    public int $maxInstances = 1;
}
