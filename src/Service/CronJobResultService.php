<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Service;

use DateTime;
use Doctrine\Common\Persistence\ManagerRegistry;
use Shapecode\Bundle\CronBundle\Entity\CronJobResultInterface;
use Shapecode\Bundle\CronBundle\Repository\CronJobResultRepositoryInterface;
use function assert;

class CronJobResultService implements CronJobResultServiceInterface
{
    /** @var ManagerRegistry */
    private $registry;

    /** @var string */
    private $pruneInterval;

    public function __construct(ManagerRegistry $registry, string $pruneInterval)
    {
        $this->registry      = $registry;
        $this->pruneInterval = $pruneInterval;
    }

    public function prune() : void
    {
        $time = new DateTime($this->pruneInterval);

        $repo = $this->registry->getRepository(CronJobResultInterface::class);
        assert($repo instanceof CronJobResultRepositoryInterface);

        $repo->deleteOldLogs($time);
    }
}
