<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Domain;

use Shapecode\Bundle\CronBundle\Entity\CronJob;
use Symfony\Component\Process\Process;

final class CronJobRunning
{
    public function __construct(
        public readonly CronJob $cronJob,
        public readonly Process $process,
    ) {
    }
}
