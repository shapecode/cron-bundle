<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Event;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Shapecode\Bundle\CronBundle\Model\CronJobMetadata;
use Symfony\Contracts\EventDispatcher\Event;

use function array_values;
use function count;

/**
 * @template-implements IteratorAggregate<int, CronJobMetadata>
 */
final class LoadJobsEvent extends Event implements IteratorAggregate, Countable
{
    /**
     * @param list<CronJobMetadata> $jobs
     */
    public function __construct(
        private array $jobs = []
    ) {
    }

    public function addJob(CronJobMetadata $cronJobMetadata): void
    {
        $this->jobs[] = $cronJobMetadata;
    }

    /**
     * @return list<CronJobMetadata>
     */
    public function getJobs(): array
    {
        return array_values($this->jobs);
    }

    /**
     * @return ArrayIterator<int, CronJobMetadata>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->jobs);
    }

    public function count(): int
    {
        return count($this->jobs);
    }
}
