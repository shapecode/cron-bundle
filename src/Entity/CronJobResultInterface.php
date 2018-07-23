<?php

namespace Shapecode\Bundle\CronBundle\Entity;

/**
 * Class CronJobResultInterface
 *
 * @package Shapecode\Bundle\CronBundle\Entity
 * @author  Nikita Loges
 */
interface CronJobResultInterface extends AbstractEntityInterface
{

    public const SUCCEEDED = 'succeeded';
    public const FAILED = 'failed';
    public const SKIPPED = 'skipped';

    public const EXIT_CODE_SUCCEEDED = 0;
    public const EXIT_CODE_FAILED = 1;
    public const EXIT_CODE_SKIPPED = 2;

    /**
     * @param \DateTime $runAt
     */
    public function setRunAt(\DateTime $runAt): void;

    /**
     * @return \DateTime
     */
    public function getRunAt(): \DateTime;

    /**
     * @param float $runTime
     */
    public function setRunTime(float $runTime): void;

    /**
     * @return float
     */
    public function getRunTime(): float;

    /**
     * @param integer $result
     */
    public function setStatusCode(int $result): void;

    /**
     * @return integer
     */
    public function getStatusCode(): int;

    /**
     * @param string $output
     */
    public function setOutput(?string $output): void;

    /**
     * @return string
     */
    public function getOutput(): ?string;

    /**
     * @param CronJobInterface $job
     */
    public function setCronJob(CronJobInterface $job): void;

    /**
     * @return CronJobInterface
     */
    public function getCronJob(): CronJobInterface;
}
