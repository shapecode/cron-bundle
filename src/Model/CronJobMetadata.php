<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Model;

use RuntimeException;
use Symfony\Component\Console\Command\Command;

use function str_replace;
use function trim;

final class CronJobMetadata
{
    private string $expression;

    private string $command;

    private ?string $description;

    private ?string $arguments;

    private int $maxInstances;

    public function __construct(
        string $expression,
        string $command,
        ?string $arguments = null,
        int $maxInstances = 1,
        ?string $description = null
    ) {
        $this->expression   = $expression;
        $this->command      = $command;
        $this->arguments    = $arguments;
        $this->maxInstances = $maxInstances;
        $this->description  = $description;
    }

    public static function createByCommand(
        string $expression,
        Command $command,
        ?string $arguments = null,
        int $maxInstances = 1
    ): CronJobMetadata {
        $commandName = $command->getName();

        if ($commandName === null) {
            throw new RuntimeException('command has to have a name provided');
        }

        return new self(
            $expression,
            $commandName,
            $arguments,
            $maxInstances,
            $command->getDescription()
        );
    }

    public function getExpression(): string
    {
        return $this->expression;
    }

    public function getClearedExpression(): string
    {
        $expression = $this->getExpression();
        $expression = str_replace('\\', '', $expression);

        return $expression;
    }

    public function getCommand(): string
    {
        return trim($this->command);
    }

    public function getArguments(): ?string
    {
        if ($this->arguments === null) {
            return null;
        }

        return trim($this->arguments);
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getMaxInstances(): int
    {
        return $this->maxInstances;
    }
}
