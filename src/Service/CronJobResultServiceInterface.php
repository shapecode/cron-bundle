<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Service;

/**
 * Interface CronJobResultServiceInterface
 */
interface CronJobResultServiceInterface
{
    public function prune() : void;
}
