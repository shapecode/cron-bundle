<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Model;

use RuntimeException;
use Symfony\Component\Console\Command\Command;

use function str_replace;
use function trim;

final class CronJobMetadata
{
    /** @var string */
    private $expression;

    /** @var string */
    private $command;

    /** @var string|null */
    private $description;

    /** @var string|null */
    private $arguments;

    /** @var int */
    private $maxInstances;

    public function __construct(string $expression, string $command, ?string $arguments = null, int $maxInstances = 1)
    {
        $this->expression   = $expression;
        $this->command      = $command;
        $this->arguments    = $arguments;
        $this->maxInstances = $maxInstances;
    }

    public static function createByCommand(string $expression, Command $command, ?string $arguments = null, int $maxInstances = 1): CronJobMetadata
    {
        $commandName = $command->getName();

        if ($commandName === null) {
            throw new RuntimeException('command has to have a name provided');
        }

        $meta = new static($expression, $commandName, $arguments, $maxInstances);
        $meta->setDescription($command->getDescription());

        return $meta;
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

    public function getFullCommand(): string
    {
        $arguments = '';

        if ($this->getArguments() !== null) {
            $arguments = ' ' . $this->getArguments();
        }

        return trim($this->getCommand() . $arguments);
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

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getMaxInstances(): int
    {
        return $this->maxInstances;
    }
}
