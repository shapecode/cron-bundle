<?php
// phpcs:disable

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Domain;

enum CronJobResultStatus
{
    case SUCCEEDED;
    case FAILED;
    case SKIPPED;

    public static function fromCommandStatus(int $statusCode): self
    {
        return match ($statusCode) {
            0 => self::SUCCEEDED,
            2 => self::SKIPPED,
            default => self::FAILED,
        };
    }

    public function getStatusMessage(): string
    {
        return match ($this) {
            self::SUCCEEDED => 'succeeded',
            self::FAILED => 'failed',
            self::SKIPPED => 'skipped',
        };
    }

    public function getBlockName(): string
    {
        return match ($this) {
            self::SUCCEEDED => 'success',
            self::FAILED => 'error',
            self::SKIPPED => 'info',
        };
    }
}
