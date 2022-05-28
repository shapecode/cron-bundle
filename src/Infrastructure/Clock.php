<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Infrastructure;

use DateTimeImmutable;
use Lcobucci\Clock\SystemClock;
use StellaMaris\Clock\ClockInterface;

final class Clock implements ClockInterface
{
    private readonly ClockInterface $clock;

    public function __construct(?ClockInterface $clock)
    {
        $this->clock = $clock ?? SystemClock::fromSystemTimezone();
    }

    public function now(): DateTimeImmutable
    {
        return $this->clock->now();
    }
}
