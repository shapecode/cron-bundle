<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Domain;

use RuntimeException;
use Symfony\Component\Console\Command\Command;

use function str_replace;

final readonly class CronJobMetadata
{
    private function __construct(
        public string $expression,
        public string $command,
        public string|null $arguments = null,
        public int $maxInstances = 1,
        public string|null $description = null,
    ) {
    }

    public static function createByCommand(
        string $expression,
        Command $command,
        string|null $arguments = null,
        int $maxInstances = 1,
    ): CronJobMetadata {
        $commandName = $command->getName();

        if ($commandName === null) {
            throw new RuntimeException('command has to have a name provided', 1653426725688);
        }

        return new self(
            str_replace('\\', '', $expression),
            $commandName,
            $arguments,
            $maxInstances,
            $command->getDescription(),
        );
    }
}
