<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\Collection;

interface CronJobInterface extends AbstractEntityInterface
{
    public function setCommand(string $command): void;

    public function getCommand(): string;

    public function getFullCommand(): string;

    public function getArguments(): ?string;

    public function setArguments(?string $arguments): void;

    public function setDescription(?string $description): void;

    public function getDescription(): ?string;

    public function getRunningInstances(): int;

    public function setRunningInstances(int $runningInstances): void;

    public function getMaxInstances(): int;

    public function setMaxInstances(int $maxInstances): void;

    public function increaseRunningInstances(): void;

    public function decreaseRunningInstances(): void;

    public function getNumber(): int;

    public function setNumber(int $number): void;

    public function setPeriod(string $period): void;

    public function getPeriod(): string;

    public function setNextRun(DateTime $nextRun): void;

    public function getNextRun(): DateTime;

    /**
     * @return Collection|CronJobResult[]
     */
    public function getResults(): Collection;

    public function getLastUse(): ?DateTime;

    public function setLastUse(DateTime $lastUse): void;

    public function hasResult(CronJobResultInterface $result): bool;

    public function addResult(CronJobResultInterface $result): void;

    public function removeResult(CronJobResultInterface $result): void;

    public function setEnable(bool $enable): void;

    /**
     * @see getIsEnable()
     */
    public function isEnable(): bool;

    public function calculateNextRun(): void;
}
