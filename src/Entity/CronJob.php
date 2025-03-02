<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Entity;

use Cron\CronExpression;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Shapecode\Bundle\CronBundle\Repository\CronJobRepository;

#[ORM\Entity(repositoryClass: CronJobRepository::class)]
class CronJob extends AbstractEntity
{
    #[ORM\Column(type: Types::STRING)]
    private string $command;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private string|null $arguments = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private string|null $description = null;

    #[ORM\Column(type: Types::INTEGER, options: ['unsigned' => true, 'default' => 0])]
    private int $runningInstances = 0;

    #[ORM\Column(type: Types::INTEGER, options: ['unsigned' => true, 'default' => 1])]
    private int $maxInstances = 1;

    #[ORM\Column(type: Types::INTEGER, options: ['unsigned' => true, 'default' => 1])]
    private int $number = 1;

    #[ORM\Column(type: Types::STRING)]
    private string $period;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private DateTimeInterface|null $lastUse = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private DateTimeInterface $nextRun;

    /** @var Collection<int, CronJobResult>*/
    #[ORM\OneToMany(targetEntity: CronJobResult::class, mappedBy: 'cronJob', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $results;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => true])]
    private bool $enable = true;

    public function __construct(
        string $command,
        string $period,
    ) {
        $this->command = $command;
        $this->period  = $period;
        $this->results = new ArrayCollection();

        $this->calculateNextRun();
    }

    public static function create(
        string $command,
        string $period,
    ): self {
        return new self($command, $period);
    }

    public function getCommand(): string
    {
        return $this->command;
    }

    public function getFullCommand(): string
    {
        $arguments = '';

        if ($this->getArguments() !== null) {
            $arguments = ' ' . $this->getArguments();
        }

        return $this->getCommand() . $arguments;
    }

    public function getArguments(): string|null
    {
        return $this->arguments;
    }

    public function setArguments(string|null $arguments): self
    {
        $this->arguments = $arguments;

        return $this;
    }

    public function getDescription(): string|null
    {
        return $this->description;
    }

    public function setDescription(string|null $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getRunningInstances(): int
    {
        return $this->runningInstances;
    }

    public function increaseRunningInstances(): self
    {
        ++$this->runningInstances;

        return $this;
    }

    public function decreaseRunningInstances(): self
    {
        --$this->runningInstances;

        return $this;
    }

    public function getMaxInstances(): int
    {
        return $this->maxInstances;
    }

    public function setMaxInstances(int $maxInstances): self
    {
        $this->maxInstances = $maxInstances;

        return $this;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function setNumber(int $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getPeriod(): string
    {
        return $this->period;
    }

    public function setPeriod(string $period): self
    {
        $this->period = $period;

        return $this;
    }

    public function getLastUse(): DateTimeInterface|null
    {
        return $this->lastUse;
    }

    public function setLastUse(DateTimeInterface $lastUse): self
    {
        $this->lastUse = DateTime::createFromInterface($lastUse);

        return $this;
    }

    public function setNextRun(DateTimeInterface $nextRun): self
    {
        $this->nextRun = DateTime::createFromInterface($nextRun);

        return $this;
    }

    public function getNextRun(): DateTimeInterface
    {
        return $this->nextRun;
    }

    /** @return Collection<int, CronJobResult> */
    public function getResults(): Collection
    {
        return $this->results;
    }

    public function setEnable(bool $enable): self
    {
        $this->enable = $enable;

        return $this;
    }

    public function isEnable(): bool
    {
        return $this->enable;
    }

    public function enable(): self
    {
        return $this->setEnable(true);
    }

    public function disable(): self
    {
        return $this->setEnable(false);
    }

    public function calculateNextRun(): self
    {
        $cron = new CronExpression($this->getPeriod());
        $this->setNextRun($cron->getNextRunDate());

        return $this;
    }

    public function __toString(): string
    {
        return $this->getCommand();
    }
}
