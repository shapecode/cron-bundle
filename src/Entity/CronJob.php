<?php

namespace Shapecode\Bundle\CronBundle\Entity;

use Cron\CronExpression;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class CronJob
 *
 * @package Shapecode\Bundle\CronBundle\Entity
 * @author  Nikita Loges
 *
 * @ORM\Entity(repositoryClass="Shapecode\Bundle\CronBundle\Repository\CronJobRepository")
 * @ORM\HasLifecycleCallbacks
 */
class CronJob extends AbstractEntity implements CronJobInterface
{

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $command;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $arguments;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $description;

    /**
     * @var int
     * @ORM\Column(type="integer", options={"default":1})
     */
    protected $number = 1;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $period;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $lastUse;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    protected $nextRun;

    /**
     * @var Collection|CronJobResult[]
     * @ORM\OneToMany(targetEntity="Shapecode\Bundle\CronBundle\Entity\CronJobResultInterface", mappedBy="cronJob", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $results;

    /**
     * @ORM\Column(type="boolean", options={"default"=1})
     */
    protected $enable = true;

    /**
     * @inheritdoc
     */
    public function __construct()
    {
        $this->results = new ArrayCollection();
    }

    /**
     * @inheritdoc
     */
    public function setCommand(string $command): void
    {
        $this->command = $command;
    }

    /**
     * @inheritdoc
     */
    public function getCommand(): string
    {
        return $this->command;
    }

    /**
     * @inheritdoc
     */
    public function getFullCommand(): string
    {
        $arguments = '';

        if ($this->getArguments()) {
            $arguments = ' ' . $this->getArguments();
        }

        return $this->getCommand() . $arguments;
    }

    /**
     * @inheritdoc
     */
    public function getArguments(): ?string
    {
        return $this->arguments;
    }

    /**
     * @inheritdoc
     */
    public function setArguments(?string $arguments): void
    {
        $this->arguments = $arguments;
    }

    /**
     * @inheritdoc
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @inheritdoc
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @inheritdoc
     */
    public function getNumber(): int
    {
        return $this->number;
    }

    /**
     * @inheritdoc
     */
    public function setNumber(int $number)
    {
        $this->number = $number;
    }

    /**
     * @inheritdoc
     */
    public function getPeriod(): string
    {
        return $this->period;
    }

    /**
     * @inheritdoc
     */
    public function getInterval(): \DateInterval
    {
        return new \DateInterval($this->getPeriod());
    }

    /**
     * @inheritdoc
     */
    public function setPeriod(string $period): void
    {
        $this->period = $period;
    }

    /**
     * @inheritdoc
     */
    public function getLastUse(): ?\DateTime
    {
        return $this->lastUse;
    }

    /**
     * @inheritdoc
     */
    public function setLastUse(\DateTime $lastUse): void
    {
        $this->lastUse = $lastUse;
    }

    /**
     * @inheritdoc
     */
    public function setNextRun(\DateTime $nextRun): void
    {
        $this->nextRun = $nextRun;
    }

    /**
     * @inheritdoc
     */
    public function getNextRun(): \DateTime
    {
        return $this->nextRun;
    }

    /**
     * @inheritdoc
     */
    public function getResults(): Collection
    {
        return $this->results;
    }

    /**
     * @inheritdoc
     */
    public function hasResult(CronJobResultInterface $result): bool
    {
        return $this->getResults()->contains($result);
    }

    /**
     * @inheritdoc
     */
    public function addResult(CronJobResultInterface $result): void
    {
        if (!$this->hasResult($result)) {
            $result->setCronJob($this);
            $this->getResults()->add($result);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeResult(CronJobResultInterface $result): void
    {
        if ($this->hasResult($result)) {
            $this->getResults()->removeElement($result);
        }
    }

    /**
     * @param boolean $enable
     */
    public function setEnable(bool $enable): void
    {
        $this->enable = $enable;
    }

    /**
     * @return boolean
     */
    public function isEnable(): bool
    {
        return $this->enable;
    }

    /**
     *
     */
    public function calculateNextRun(): void
    {
        $cron = CronExpression::factory($this->getPeriod());
        $this->setNextRun($cron->getNextRunDate());
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->getCommand();
    }
}
