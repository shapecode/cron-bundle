<?php

namespace Shapecode\Bundle\CronBundle\Entity;

use Doctrine\Common\Collections\Collection;

/**
 * Class CronJobInterface
 *
 * @package Shapecode\Bundle\CronBundle\Entity
 * @author  Nikita Loges
 */
interface CronJobInterface extends AbstractEntityInterface
{

    /**
     * @param string $command
     */
    public function setCommand($command);

    /**
     * @return string
     */
    public function getCommand();

    /**
     * @return string
     */
    public function getArguments();

    /**
     * @param string $arguments
     */
    public function setArguments($arguments);

    /**
     * @param string $description
     */
    public function setDescription($description);

    /**
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
     * @param string $period
     */
    public function setPeriod($period);

    /**
     * @return string
     */
    public function getPeriod();

    /**
     * @param \DateTime $nextRun
     */
    public function setNextRun(\DateTime $nextRun);

    /**
     * @return \DateTime
     */
    public function getNextRun();

    /**
     * @return Collection|CronJobResult[]
     */
    public function getResults();

    /**
     * @param CronJobResultInterface $result
     *
     * @return bool
     */
    public function hasResult(CronJobResultInterface $result);

    /**
     * @param CronJobResultInterface $result
     */
    public function addResult(CronJobResultInterface $result);

    /**
     * @param CronJobResultInterface $result
     */
    public function removeResult(CronJobResultInterface $result);

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
