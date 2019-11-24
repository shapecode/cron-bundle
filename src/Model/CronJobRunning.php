<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Model;

use Shapecode\Bundle\CronBundle\Entity\CronJobInterface;
use Symfony\Component\Process\Process;

final class CronJobRunning
{
    /** @var CronJobInterface */
    private $cronJob;

    /** @var Process */
    private $process;

    public function __construct(CronJobInterface $cronJob, Process $process)
    {
        $this->cronJob = $cronJob;
        $this->process = $process;
    }

    public function getCronJob() : CronJobInterface
    {
        return $this->cronJob;
    }

    public function getProcess() : Process
    {
        return $this->process;
    }
}
