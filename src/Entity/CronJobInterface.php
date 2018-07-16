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
    public function setCommand(string $command): void;

    /**
     * @return string
     */
    public function getCommand(): string;

    /**
     * @return string
     */
    public function getFullCommand(): string;

    /**
     * @return string
     */
    public function getArguments(): ?string;

    /**
     * @param string $arguments
     */
    public function setArguments(?string $arguments): void;

    /**
     * @param string $description
     */
    public function setDescription(?string $description): void;

    /**
     * @return string
     */
    public function getDescription(): ?string;

    /**
     * @return int
     */
    public function getNumber(): ?int;

    /**
     * @param int $number
     */
    public function setNumber(int $number);

    /**
     * @param string $period
     */
    public function setPeriod(string $period): void;

    /**
     * @return string
     */
    public function getPeriod(): string;

    /**
     * @param \DateTime $nextRun
     */
    public function setNextRun(\DateTime $nextRun): void;

    /**
     * @return \DateTime
     */
    public function getNextRun(): \DateTime;

    /**
     * @return Collection|CronJobResult[]
     */
    public function getResults(): Collection;

    /**
     * @return \DateTime
     */
    public function getLastUse(): ?\DateTime;

    /**
     * @param $lastUse
     */
    public function setLastUse(\DateTime $lastUse): void;

    /**
     * @param CronJobResultInterface $result
     *
     * @return bool
     */
    public function hasResult(CronJobResultInterface $result): bool ;

    /**
     * @param CronJobResultInterface $result
     */
    public function addResult(CronJobResultInterface $result): void;

    /**
     * @param CronJobResultInterface $result
     */
    public function removeResult(CronJobResultInterface $result): void;

    /**
     * @param boolean $enable
     */
    public function setEnable(bool $enable): void;

    /**
     * @see getIsEnable()
     */
    public function isEnable(): bool;

    /**
     *
     */
    public function calculateNextRun(): void;
}
