<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final class AsCronJob
{
    public function __construct(
        public readonly string $schedule,
        public readonly ?string $arguments = null,
        public readonly int $maxInstances = 1,
    ) {
    }
}
