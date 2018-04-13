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

    const SUCCEEDED = 'succeeded';
    const FAILED = 'failed';
    const SKIPPED = 'skipped';

    const EXIT_CODE_SUCCEEDED = 0;
    const EXIT_CODE_FAILED = 1;
    const EXIT_CODE_SKIPPED = 1;

    /**
     * @return integer
     */
    public function getId();

    /**
     * @param integer|null $id
     */
    public function setId($id = null);

    /**
     * @param \DateTime $runAt
     */
    public function setRunAt(\DateTime $runAt);

    /**
     * @return \DateTime
     */
    public function getRunAt();

    /**
     * @param float $runTime
     */
    public function setRunTime($runTime);

    /**
     * @return float
     */
    public function getRunTime();

    /**
     * @param integer $result
     */
    public function setStatusCode($result);

    /**
     * @return integer
     */
    public function getStatusCode();

    /**
     * @param string $output
     */
    public function setOutput($output);

    /**
     * @return string
     */
    public function getOutput();

    /**
     * @param CronJobInterface $job
     */
    public function setCronJob(CronJobInterface $job);

    /**
     * @return CronJobInterface
     */
    public function getCronJob();

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt);

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt);

    /**
     * @return \DateTime
     */
    public function getUpdatedAt();
}
