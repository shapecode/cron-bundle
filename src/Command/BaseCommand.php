<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Command;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Shapecode\Bundle\CronBundle\Entity\CronJobInterface;
use Shapecode\Bundle\CronBundle\Entity\CronJobResultInterface;
use Shapecode\Bundle\CronBundle\Repository\CronJobRepositoryInterface;
use Shapecode\Bundle\CronBundle\Repository\CronJobResultRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use function assert;

abstract class BaseCommand extends Command
{
    /** @var ManagerRegistry */
    private $registry;

    public function __construct(
        ManagerRegistry $registry
    ) {
        parent::__construct();

        $this->registry = $registry;
    }

    final protected function getManager() : ObjectManager
    {
        return $this->registry->getManager();
    }

    final protected function getCronJobRepository() : CronJobRepositoryInterface
    {
        $repo = $this->registry->getRepository(CronJobInterface::class);
        assert($repo instanceof CronJobRepositoryInterface);

        return $repo;
    }

    final protected function getCronJobResultRepository() : CronJobResultRepositoryInterface
    {
        $repo = $this->registry->getRepository(CronJobResultInterface::class);
        assert($repo instanceof CronJobResultRepositoryInterface);

        return $repo;
    }
}
