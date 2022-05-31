<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Collection;

use Ramsey\Collection\Collection;
use Shapecode\Bundle\CronBundle\Domain\CronJobMetadata;

/**
 * @template-extends Collection<CronJobMetadata>
 */
final class CronJobMetadataCollection extends Collection
{
    public function __construct(
        CronJobMetadata ...$metadata
    ) {
        parent::__construct(CronJobMetadata::class, $metadata);
    }
}
