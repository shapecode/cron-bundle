<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Entity;

use Cron\CronExpression;
use DateInterval;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;

/**
 * @ORM\Entity(repositoryClass="Shapecode\Bundle\CronBundle\Repository\CronJobRepository")
 */
class CronJob extends AbstractEntity implements CronJobInterface
{
    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $command;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string|null
     */
    protected $arguments;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string|null
     */
    protected $description;

    /**
     * @ORM\Column(type="integer", options={"unsigned": true, "default":0})
     *
     * @var int
     */
    protected $runningInstances = 0;

    /**
     * @ORM\Column(type="integer", options={"unsigned": true, "default":1})
     *
     * @var int
     */
    protected $maxInstances = 1;

    /**
     * @ORM\Column(type="integer", options={"unsigned": true, "default":1})
     *
     * @var int
     */
    protected $number = 1;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $period;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var DateTime|null
     */
    protected $lastUse;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var DateTime
     */
    protected $nextRun;

    /**
     * @ORM\OneToMany(targetEntity="Shapecode\Bundle\CronBundle\Entity\CronJobResultInterface", mappedBy="cronJob", cascade={"persist", "remove"}, orphanRemoval=true)
     *
     * @var ArrayCollection|PersistentCollection|Collection|CronJobResult[]
     */
    protected $results;

    /**
     * @ORM\Column(type="boolean", options={"default"=1})
     *
     * @var bool
     */
    protected $enable = true;

    public function __construct()
    {
        parent::__construct();

        $this->results = new ArrayCollection();
    }

    public function setCommand(string $command) : void
    {
        $this->command = $command;
    }

    public function getCommand() : string
    {
        return $this->command;
    }

    public function getFullCommand() : string
    {
        $arguments = '';

        if ($this->getArguments() !== null) {
            $arguments = ' ' . $this->getArguments();
        }

        return $this->getCommand() . $arguments;
    }

    public function getArguments() : ?string
    {
        return $this->arguments;
    }

    public function setArguments(?string $arguments) : void
    {
        $this->arguments = $arguments;
    }

    public function getDescription() : ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description) : void
    {
        $this->description = $description;
    }

    public function getRunningInstances() : int
    {
        return $this->runningInstances;
    }

    public function setRunningInstances(int $runningInstances) : void
    {
        $this->runningInstances = $runningInstances;
    }

    public function increaseRunningInstances() : void
    {
        ++$this->runningInstances;
    }

    public function decreaseRunningInstances() : void
    {
        --$this->runningInstances;
    }

    public function getMaxInstances() : int
    {
        return $this->maxInstances;
    }

    public function setMaxInstances(int $maxInstances) : void
    {
        $this->maxInstances = $maxInstances;
    }

    public function getNumber() : int
    {
        return $this->number;
    }

    public function setNumber(int $number) : void
    {
        $this->number = $number;
    }

    public function getPeriod() : string
    {
        return $this->period;
    }

    public function getInterval() : DateInterval
    {
        return new DateInterval($this->getPeriod());
    }

    public function setPeriod(string $period) : void
    {
        $this->period = $period;
    }

    public function getLastUse() : ?DateTime
    {
        return $this->lastUse;
    }

    public function setLastUse(DateTime $lastUse) : void
    {
        $this->lastUse = $lastUse;
    }

    public function setNextRun(DateTime $nextRun) : void
    {
        $this->nextRun = $nextRun;
    }

    public function getNextRun() : DateTime
    {
        return $this->nextRun;
    }

    /**
     * @inheritDoc
     */
    public function getResults() : Collection
    {
        return $this->results;
    }

    public function hasResult(CronJobResultInterface $result) : bool
    {
        return $this->getResults()->contains($result);
    }

    public function addResult(CronJobResultInterface $result) : void
    {
        if ($this->hasResult($result)) {
            return;
        }

        $result->setCronJob($this);
        $this->getResults()->add($result);
    }

    public function removeResult(CronJobResultInterface $result) : void
    {
        if (! $this->hasResult($result)) {
            return;
        }

        $this->getResults()->removeElement($result);
    }

    public function setEnable(bool $enable) : void
    {
        $this->enable = $enable;
    }

    public function isEnable() : bool
    {
        return $this->enable;
    }

    public function calculateNextRun() : void
    {
        $cron = CronExpression::factory($this->getPeriod());
        $this->setNextRun($cron->getNextRunDate());
    }

    public function __toString() : string
    {
        return $this->getCommand();
    }
}
