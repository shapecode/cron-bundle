<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Command;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Shapecode\Bundle\CronBundle\Entity\CronJob;
use Shapecode\Bundle\CronBundle\Entity\CronJobResult;
use Shapecode\Bundle\CronBundle\Repository\CronJobRepository;
use Shapecode\Bundle\CronBundle\Repository\CronJobResultRepository;
use Symfony\Component\Console\Command\Command;

abstract class BaseCommand extends Command
{
    private ManagerRegistry $registry;

    public function __construct(
        ManagerRegistry $registry
    ) {
        parent::__construct();

        $this->registry = $registry;
    }

    final protected function getManager(): ObjectManager
    {
        return $this->registry->getManager();
    }

    final protected function getCronJobRepository(): CronJobRepository
    {
        return $this->registry->getRepository(CronJob::class);
    }

    final protected function getCronJobResultRepository(): CronJobResultRepository
    {
        return $this->registry->getRepository(CronJobResult::class);
    }
}
