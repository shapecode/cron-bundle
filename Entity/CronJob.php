<?php
namespace Shapecode\Bundle\CronBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Shapecode\Bundle\CronBundle\Entity\Plan\CronJobInterface;

/**
 * Class CronJob
 * @package Shapecode\Bundle\CronBundle\Entity
 * @author Nikita Loges
 *
 * @ORM\Entity(repositoryClass="Shapecode\Bundle\CronBundle\Repository\CronJobRepository")
 * @ORM\HasLifecycleCallbacks
 */
class CronJob extends BaseEntity implements CronJobInterface
{

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $command;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $description;

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
    protected $isEnable = true;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->results = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id = null)
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function setCommand($command)
    {
        $this->command = $command;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function setNextRun($nextRun)
    {
        $this->nextRun = $nextRun;
    }

    /**
     * {@inheritdoc}
     */
    public function getNextRun()
    {
        return $this->nextRun;
    }

    /**
     * {@inheritdoc}
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * {@inheritdoc}
     */
    public function hasResult(CronJobResult $result)
    {
        return $this->getResults()->contains($result);
    }

    /**
     * {@inheritdoc}
     */
    public function addResult(CronJobResult $result)
    {
        if (!$this->hasResult($result)) {
            $result->setCronJob($this);
            $this->getResults()->add($result);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeResult(CronJobResult $result)
    {
        if ($this->hasResult($result)) {
            $this->getResults()->removeElement($result);
        }
    }

    /**
     * @param boolean $isEnable
     */
    public function setIsEnable($isEnable)
    {
        $this->isEnable = $isEnable;
    }

    /**
     * @return boolean
     */
    public function getIsEnable()
    {
        return $this->isEnable;
    }

    /**
     * @return boolean
     */
    public function isEnable()
    {
        return $this->getIsEnable();
    }

    /**
     *
     */
    public function calculateNextRun()
    {
        $now = new \DateTime();
        $nextRun = new \DateTime(date('Y') . '-' . date('m') . '-01');
        do {
            $nextRun->add($this->getInterval());
        } while ($nextRun <= $now);

        $this->setNextRun($nextRun);
    }
}
