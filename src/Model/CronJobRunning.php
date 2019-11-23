<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Model;

use Shapecode\Bundle\CronBundle\Entity\CronJobInterface;
use Symfony\Component\Process\Process;

class CronJobRunning
{
    /** @var CronJobInterface */
    protected $cronJob;

    /** @var Process */
    protected $process;

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
