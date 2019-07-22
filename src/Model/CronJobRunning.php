<?php

namespace Shapecode\Bundle\CronBundle\Model;

use Shapecode\Bundle\CronBundle\Entity\CronJobInterface;
use Symfony\Component\Process\Process;

/**
 * Class CronJobRunning
 *
 * @package Shapecode\Bundle\CronBundle\Model
 * @author  Nikita Loges
 */
class CronJobRunning
{

    /** @var CronJobInterface */
    protected $cronJob;

    /** @var Process */
    protected $process;

    /**
     * @param CronJobInterface $cronJob
     * @param Process          $process
     */
    public function __construct(CronJobInterface $cronJob, Process $process)
    {
        $this->cronJob = $cronJob;
        $this->process = $process;
    }

    /**
     * @return CronJobInterface
     */
    public function getCronJob(): CronJobInterface
    {
        return $this->cronJob;
    }

    /**
     * @return Process
     */
    public function getProcess(): Process
    {
        return $this->process;
    }
}
