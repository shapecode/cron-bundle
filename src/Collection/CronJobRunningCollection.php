<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Collection;

use Ramsey\Collection\Collection;
use Shapecode\Bundle\CronBundle\Model\CronJobRunning;

/**
 * @template-extends Collection<CronJobRunning>
 */
final class CronJobRunningCollection extends Collection
{
    public function __construct(
        CronJobRunning ...$runnings
    ) {
        parent::__construct(CronJobRunning::class, $runnings);
    }
}
