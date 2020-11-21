<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Model;

use Shapecode\Bundle\CronBundle\Entity\CronJob;
use Symfony\Component\Process\Process;

final class CronJobRunning
{
    private CronJob $cronJob;

    private Process $process;

    public function __construct(
        CronJob $cronJob,
        Process $process
    ) {
        $this->cronJob = $cronJob;
        $this->process = $process;
    }

    public function getCronJob(): CronJob
    {
        return $this->cronJob;
    }

    public function getProcess(): Process
    {
        return $this->process;
    }
}
