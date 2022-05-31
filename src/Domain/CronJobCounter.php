<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Domain;

use function array_key_exists;

final class CronJobCounter
{
    /** @var array<string, int> */
    private array $counter = [];

    public function increase(CronJobMetadata $metadata): void
    {
        if (! array_key_exists($metadata->command, $this->counter)) {
            $this->counter[$metadata->command] = 0;
        }

        $this->counter[$metadata->command]++;
    }

    public function value(CronJobMetadata $metadata): int
    {
        if (! array_key_exists($metadata->command, $this->counter)) {
            return 0;
        }

        return $this->counter[$metadata->command];
    }
}
