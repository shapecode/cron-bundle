<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Shapecode\Bundle\CronBundle\Repository\CronJobResultRepository")
 */
class CronJobResult extends AbstractEntity implements CronJobResultInterface
{
    /**
     * @ORM\Column(type="datetime")
     *
     * @var DateTime
     */
    protected $runAt;

    /**
     * @ORM\Column(type="float")
     *
     * @var float
     */
    protected $runTime;

    /**
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    protected $statusCode;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string|null
     */
    protected $output;

    /**
     * @ORM\ManyToOne(targetEntity="Shapecode\Bundle\CronBundle\Entity\CronJob", inversedBy="results", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     *
     * @var CronJobInterface
     */
    protected $cronJob;

    public function __construct()
    {
        parent::__construct();

        $this->runAt = new DateTime();
    }

    public function setRunAt(DateTime $runAt) : void
    {
        $this->runAt = $runAt;
    }

    public function getRunAt() : DateTime
    {
        return $this->runAt;
    }

    public function setRunTime(float $runTime) : void
    {
        $this->runTime = $runTime;
    }

    public function getRunTime() : float
    {
        return $this->runTime;
    }

    public function getStatusCode() : int
    {
        return $this->statusCode;
    }

    public function setStatusCode(int $statusCode) : void
    {
        $this->statusCode = $statusCode;
    }

    public function setOutput(?string $output) : void
    {
        $this->output = $output;
    }

    public function getOutput() : ?string
    {
        return $this->output;
    }

    public function setCronJob(CronJobInterface $job) : void
    {
        $this->cronJob = $job;
    }

    public function getCronJob() : CronJobInterface
    {
        return $this->cronJob;
    }

    public function __toString() : string
    {
        return $this->getCronJob()->getCommand() . ' - ' . $this->getRunAt()->format('d.m.Y H:i P');
    }
}
