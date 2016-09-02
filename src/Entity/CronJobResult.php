<?php
namespace Shapecode\Bundle\CronBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Shapecode\Bundle\CronBundle\Entity\Interfaces\CronJobResultInterface;

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
     * @ORM\Column(type="text")
     */
    protected $output;

    /**
     * @var CronJob
     * @ORM\ManyToOne(targetEntity="CronJob", inversedBy="results")
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
    public function setRunAt(\DateTime $runAt)
    {
        $this->runAt = $runAt;
    }

    /**
     * @inheritdoc
     */
    public function getRunAt()
    {
        return $this->runAt;
    }

    /**
     * @inheritdoc
     */
    public function setRunTime($runTime)
    {
        $this->runTime = $runTime;
    }

    /**
     * @inheritdoc
     */
    public function getRunTime()
    {
        return $this->runTime;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param int $statusCode
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
    }

    /**
     * @inheritdoc
     */
    public function setOutput($output)
    {
        $this->output = $output;
    }

    /**
     * @inheritdoc
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @inheritdoc
     */
    public function setCronJob(CronJob $job)
    {
        $this->cronJob = $job;
    }

    /**
     * @inheritdoc
     */
    public function getCronJob()
    {
        return $this->cronJob;
    }
}
