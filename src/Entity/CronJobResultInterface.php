<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Entity;

use DateTime;

interface CronJobResultInterface extends AbstractEntityInterface
{
    public const SUCCEEDED = 'succeeded';
    public const FAILED    = 'failed';
    public const SKIPPED   = 'skipped';

    public const EXIT_CODE_SUCCEEDED = 0;
    public const EXIT_CODE_FAILED    = 1;
    public const EXIT_CODE_SKIPPED   = 2;

    public function setRunAt(DateTime $runAt) : void;

    public function getRunAt() : DateTime;

    public function setRunTime(float $runTime) : void;

    public function getRunTime() : float;

    public function setStatusCode(int $result) : void;

    public function getStatusCode() : int;

    public function setOutput(?string $output) : void;

    public function getOutput() : ?string;

    public function setCronJob(CronJobInterface $job) : void;

    public function getCronJob() : CronJobInterface;
}
