<?php

namespace Shapecode\Bundle\CronBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class CronJobResult
 *
 * @package Shapecode\Bundle\CronBundle\Entity
 * @author  Nikita Loges
 *
 * @ORM\Entity(repositoryClass="Shapecode\Bundle\CronBundle\Repository\CronJobResultRepository")
 * @ORM\HasLifecycleCallbacks
 */
class CronJobResult extends AbstractEntity implements CronJobResultInterface
{

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    protected $runAt;

    /**
     * @var float
     * @ORM\Column(type="float")
     */
    protected $runTime;

    /**
     * @var integer
     * @ORM\Column(type="integer")
     */
    protected $statusCode;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    protected $output;

    /**
     * @var CronJob
     * @ORM\ManyToOne(targetEntity="Shapecode\Bundle\CronBundle\Entity\CronJob", inversedBy="results", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    protected $cronJob;

    /**
     * @inheritdoc
     */
    public function __construct()
    {
        $this->runAt = new \DateTime();
    }

    /**
     * @inheritdoc
     */
    public function setRunAt(\DateTime $runAt): void
    {
        $this->runAt = $runAt;
    }

    /**
     * @inheritdoc
     */
    public function getRunAt(): \DateTime
    {
        return $this->runAt;
    }

    /**
     * @inheritdoc
     */
    public function setRunTime(float $runTime): void
    {
        $this->runTime = $runTime;
    }

    /**
     * @inheritdoc
     */
    public function getRunTime(): float
    {
        return $this->runTime;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @param int $statusCode
     */
    public function setStatusCode(int $statusCode): void
    {
        $this->statusCode = $statusCode;
    }

    /**
     * @inheritdoc
     */
    public function setOutput(?string $output): void
    {
        $this->output = $output;
    }

    /**
     * @inheritdoc
     */
    public function getOutput(): ?string
    {
        return $this->output;
    }

    /**
     * @inheritdoc
     */
    public function setCronJob(CronJobInterface $job): void
    {
        $this->cronJob = $job;
    }

    /**
     * @inheritdoc
     */
    public function getCronJob(): CronJobInterface
    {
        return $this->cronJob;
    }
}
