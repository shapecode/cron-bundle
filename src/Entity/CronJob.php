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
     * @ORM\Column(type="integer", options={"unsigned": true, "default":0})
     */
    protected $runningInstances = 0;

    /**
     * @var int
     * @ORM\Column(type="integer", options={"unsigned": true, "default":1})
     */
    protected $maxInstances = 1;

    /**
     * @var int
     * @ORM\Column(type="integer", options={"unsigned": true, "default":1})
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
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function setId($id = null)
    {
        $this->id = $id;
    }

    /**
     * @inheritdoc
     */
    public function setCommand($command)
    {
        $this->command = $command;
    }

    /**
     * @inheritdoc
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * @inheritdoc
     */
    public function getFullCommand()
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
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * @inheritdoc
     */
    public function setArguments($arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @inheritdoc
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @inheritdoc
     */
    public function getRunningInstances()
    {
        return $this->runningInstances;
    }

    /**
     * @inheritdoc
     */
    public function setRunningInstances($runningInstances)
    {
        $this->runningInstances = $runningInstances;
    }

    /**
     *
     */
    public function increaseRunningInstances()
    {
        $this->runningInstances += 1;
    }

    /**
     *
     */
    public function decreaseRunningInstances()
    {
        $this->runningInstances -= 1;
    }

    /**
     * @return int
     */
    public function getMaxInstances()
    {
        return $this->maxInstances;
    }

    /**
     * @param int $maxInstances
     */
    public function setMaxInstances($maxInstances)
    {
        $this->maxInstances = $maxInstances;
    }

    /**
     * @inheritdoc
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @inheritdoc
     */
    public function setNumber($number)
    {
        $this->number = $number;
    }

    /**
     * @inheritdoc
     */
    public function getPeriod()
    {
        return $this->period;
    }

    /**
     * @inheritdoc
     */
    public function getInterval()
    {
        return new \DateInterval($this->getPeriod());
    }

    /**
     * @inheritdoc
     */
    public function setPeriod($period)
    {
        $this->period = $period;
    }

    /**
     * @inheritdoc
     */
    public function getLastUse()
    {
        return $this->lastUse;
    }

    /**
     * @inheritdoc
     */
    public function setLastUse(\DateTime $lastUse)
    {
        $this->lastUse = $lastUse;
    }

    /**
     * @inheritdoc
     */
    public function setNextRun(\DateTime $nextRun)
    {
        $this->nextRun = $nextRun;
    }

    /**
     * @inheritdoc
     */
    public function getNextRun()
    {
        return $this->nextRun;
    }

    /**
     * @inheritdoc
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * @inheritdoc
     */
    public function hasResult(CronJobResultInterface $result)
    {
        return $this->getResults()->contains($result);
    }

    /**
     * @inheritdoc
     */
    public function addResult(CronJobResultInterface $result)
    {
        if (!$this->hasResult($result)) {
            $result->setCronJob($this);
            $this->getResults()->add($result);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeResult(CronJobResultInterface $result)
    {
        if ($this->hasResult($result)) {
            $this->getResults()->removeElement($result);
        }
    }

    /**
     * @param boolean $enable
     */
    public function setEnable($enable)
    {
        $this->enable = $enable;
    }

    /**
     * @return boolean
     */
    public function isEnable()
    {
        return $this->enable;
    }

    /**
     *
     */
    public function calculateNextRun()
    {
        $cron = CronExpression::factory($this->getPeriod());
        $this->setNextRun($cron->getNextRunDate());
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        return $this->getCommand();
    }
}
