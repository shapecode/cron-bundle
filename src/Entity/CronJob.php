<?php
namespace Shapecode\Bundle\CronBundle\Entity;

use Cron\CronExpression;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Shapecode\Bundle\CronBundle\Entity\Interfaces\CronJobInterface;

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
     * @ORM\OneToMany(targetEntity="CronJobResult", mappedBy="cronJob", cascade={"persist", "remove"}, orphanRemoval=true)
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
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param int $number
     */
    public function setNumber($number)
    {
        $this->number = $number;
    }

    /**
     * @return string
     */
    public function getPeriod()
    {
        return $this->period;
    }

    /**
     * @return \DateInterval
     */
    public function getInterval()
    {
        return new \DateInterval($this->getPeriod());
    }

    /**
     * @param string $period
     */
    public function setPeriod($period)
    {
        $this->period = $period;
    }

    /**
     * @return \DateTime
     */
    public function getLastUse()
    {
        return $this->lastUse;
    }

    /**
     * @param \DateTime $lastUse
     */
    public function setLastUse($lastUse)
    {
        $this->lastUse = $lastUse;
    }

    /**
     * @inheritdoc
     */
    public function setNextRun($nextRun)
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
    public function hasResult(CronJobResult $result)
    {
        return $this->getResults()->contains($result);
    }

    /**
     * @inheritdoc
     */
    public function addResult(CronJobResult $result)
    {
        if (!$this->hasResult($result)) {
            $result->setCronJob($this);
            $this->getResults()->add($result);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeResult(CronJobResult $result)
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
}
