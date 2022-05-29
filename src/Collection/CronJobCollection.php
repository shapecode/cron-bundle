<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Collection;

use Ramsey\Collection\Collection;
use Ramsey\Collection\CollectionInterface;
use Shapecode\Bundle\CronBundle\Entity\CronJob;

/**
 * @template-extends Collection<CronJob>
 */
final class CronJobCollection extends Collection
{
    public function __construct(
        CronJob ...$cronJob
    ) {
        parent::__construct(CronJob::class, $cronJob);
    }

    /**
     * @return CollectionInterface<string>
     */
    public function mapToCommand(): CollectionInterface
    {
        return $this->map(static fn (CronJob $o): string => $o->getCommand());
    }
}
