<?php

namespace Shapecode\Bundle\CronBundle\Entity\Plan;

use Shapecode\Bundle\CronBundle\Entity\CronJobResult;
use Symfony\Component\Validator\Constraints\Collection;

/**
 * Class CronJobInterface
 * @package Shapecode\Bundle\CronBundle\Entity\Plan
 * @author Nikita Loges
 * @date 02.02.2015
 */
interface CronJobInterface extends BaseEntityInterface
{


    /**
     * Set command
     *
     * @param string $command
     */
    public function setCommand($command);

    /**
     * Get command
     *
     * @return string
     */
    public function getCommand();

    /**
     * Set description
     *
     * @param string $description
     */
    public function setDescription($description);

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription();

    /**
     * Set Period
     *
     * @param string $period
     */
    public function setPeriod($period);

    /**
     * Get Period
     *
     * @return string
     */
    public function getPeriod();

    /**
     * Set nextRun
     *
     * @param \DateTime $nextRun
     */
    public function setNextRun($nextRun);

    /**
     * Get nextRun
     *
     * @return \DateTime
     */
    public function getNextRun();

    /**
     * Get results
     *
     * @return Collection|CronJobResult[]
     */
    public function getResults();

    /**
     * @param CronJobResult $result
     * @return bool
     */
    public function hasResult(CronJobResult $result);

    /**
     * Add result
     *
     * @param CronJobResult $result
     */
    public function addResult(CronJobResult $result);

    /**
     * @param CronJobResult $result
     */
    public function removeResult(CronJobResult $result);

    /**
     * @param boolean $isEnable
     */
    public function setIsEnable($isEnable);

    /**
     * @return boolean
     */
    public function getIsEnable();

    /**
     * @see getIsEnable()
     */
    public function isEnable();
}
