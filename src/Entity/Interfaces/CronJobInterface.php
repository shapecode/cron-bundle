<?php

namespace Shapecode\Bundle\CronBundle\Entity\Interfaces;

use Doctrine\Common\Collections\Collection;
use Shapecode\Bundle\CronBundle\Entity\CronJobResult;

/**
 * Class CronJobInterface
 * @package Shapecode\Bundle\CronBundle\Entity\Interfaces
 * @author Nikita Loges
 * @date 02.02.2015
 */
interface CronJobInterface extends AbstractEntityInterface
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
     * @return int
     */
    public function getNumber();

    /**
     * @param int $number
     */
    public function setNumber($number);

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
     * @param boolean $enable
     */
    public function setEnable($enable);

    /**
     * @see getIsEnable()
     */
    public function isEnable();

    /**
     *
     */
    public function calculateNextRun();
}
