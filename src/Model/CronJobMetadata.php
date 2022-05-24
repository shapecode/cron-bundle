<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Model;

use RuntimeException;
use Symfony\Component\Console\Command\Command;

use function str_replace;

final class CronJobMetadata
{
    private function __construct(
        public readonly string $expression,
        public readonly string $command,
        public readonly ?string $arguments = null,
        public readonly int $maxInstances = 1,
        public readonly ?string $description = null
    ) {
    }

    public static function createByCommand(
        string $expression,
        Command $command,
        ?string $arguments = null,
        int $maxInstances = 1
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
            $command->getDescription()
        );
    }
}
