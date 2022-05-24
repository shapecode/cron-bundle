<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Event;

use Shapecode\Bundle\CronBundle\Model\CronJobMetadata;
use Symfony\Contracts\EventDispatcher\Event;

use function array_values;

final class LoadJobsEvent extends Event
{
    /** @deprecated  */
    public const NAME = 'shapecode_cron.load_jobs';

    /** @var array<string, CronJobMetadata> */
    private array $jobs = [];

    public function addJob(CronJobMetadata $cronJobMetadata): void
    {
        $this->jobs[$cronJobMetadata->command] = $cronJobMetadata;
    }

    /**
     * @return list<CronJobMetadata>
     */
    public function getJobs(): array
    {
        return array_values($this->jobs);
    }
}
