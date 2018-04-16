<?php

namespace Shapecode\Bundle\CronBundle\Service;

use Doctrine\Common\Persistence\ManagerRegistry;
use Shapecode\Bundle\CronBundle\Entity\CronJobResultInterface;
use Shapecode\Bundle\CronBundle\Repository\CronJobResultRepositoryInterface;

/**
 * Class CronJobResultService
 *
 * @package Shapecode\Bundle\CronBundle\Service
 * @author  Nikita Loges
 * @company tenolo GbR
 */
class CronJobResultService implements CronJobResultServiceInterface
{

    /** @var ManagerRegistry */
    protected $registry;

    /** @var string */
    protected $pruneInterval;

    /**
     * @param ManagerRegistry $registry
     * @param string          $pruneInterval
     */
    public function __construct(ManagerRegistry $registry, string $pruneInterval)
    {
        $this->registry = $registry;
        $this->pruneInterval = $pruneInterval;
    }

    /**
     *
     */
    public function prune()
    {
        $time = new \DateTime($this->pruneInterval);

        /** @var CronJobResultRepositoryInterface $repo */
        $repo = $this->registry->getRepository(CronJobResultInterface::class);

        $repo->deleteOldLogs($time);
    }
}
