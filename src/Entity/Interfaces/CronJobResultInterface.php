<?php

namespace Shapecode\Bundle\CronBundle\Entity\Interfaces;

use Shapecode\Bundle\CronBundle\Entity\CronJob;

/**
 * Class CronJobResultInterface
 * @package Shapecode\Bundle\CronBundle\Entity\Interfaces
 * @author Nikita Loges
 * @date 02.02.2015
 */
interface CronJobResultInterface extends AbstractEntityInterface
{

    const SUCCEEDED = 'succeeded';
    const FAILED = 'failed';
    const SKIPPED = 'skipped';

    /**
     * Get id
     *
     * @return integer
     */
    public function getId();

    /**
     * @param integer|null $id
     */
    public function setId($id = null);

    /**
     * Set runAt
     *
     * @param \DateTime $runAt
     */
    public function setRunAt(\DateTime $runAt);

    /**
     * Get runAt
     *
     * @return \DateTime
     */
    public function getRunAt();

    /**
     * Set runTime
     *
     * @param float $runTime
     */
    public function setRunTime($runTime);

    /**
     * Get runTime
     *
     * @return float
     */
    public function getRunTime();

    /**
     * Set status code
     *
     * @param integer $result
     */
    public function setStatusCode($result);

    /**
     * Get status code
     *
     * @return integer
     */
    public function getStatusCode();

    /**
     * Set output
     *
     * @param string $output
     */
    public function setOutput($output);

    /**
     * Get output
     *
     * @return string
     */
    public function getOutput();

    /**
     * Set job
     *
     * @param CronJob $job
     */
    public function setCronJob(CronJob $job);

    /**
     * Get job
     *
     * @return CronJob
     */
    public function getCronJob();

    /**
     * Set created
     *
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt);

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * Set created
     *
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt);

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getUpdatedAt();
}
