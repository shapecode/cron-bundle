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
     * @param string $command
     */
    public function setCommand($command);

    /**
     * @return string
     */
    public function getCommand();

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
