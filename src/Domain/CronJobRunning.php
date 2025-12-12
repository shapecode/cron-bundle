<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Domain;

use Shapecode\Bundle\CronBundle\Entity\CronJob;
use Symfony\Component\Process\Process;

final readonly class CronJobRunning
{
    public function __construct(
        public CronJob $cronJob,
        public Process $process,
    ) {
    }
}
