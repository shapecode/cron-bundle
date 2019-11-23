<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Model;

use Symfony\Component\Console\Command\Command;
use function str_replace;
use function trim;

class CronJobMetadata
{
    /** @var string */
    protected $expression;

    /** @var string */
    protected $command;

    /** @var string|null */
    protected $description;

    /** @var string */
    protected $arguments;

    /** @var int */
    protected $maxInstances;

    public function __construct(string $expression, string $command, ?string $arguments = null, int $maxInstances = 1)
    {
        $this->expression   = $expression;
        $this->command      = $command;
        $this->arguments    = $arguments;
        $this->maxInstances = $maxInstances;
    }

    public static function createByCommand(string $expression, Command $command, ?string $arguments = null, int $maxInstances = 1) : CronJobMetadata
    {
        $meta = new static($expression, $command->getName(), $arguments, $maxInstances);
        $meta->setDescription($command->getDescription());

        return $meta;
    }

    public function getExpression() : string
    {
        return $this->expression;
    }

    public function getClearedExpression() : string
    {
        $expression = $this->getExpression();
        $expression = str_replace('\\', '', $expression);

        return $expression;
    }

    public function getFullCommand() : string
    {
        $arguments = '';

        if (! empty($this->getArguments())) {
            $arguments = ' ' . $this->getArguments();
        }

        return trim($this->getCommand() . $arguments);
    }

    public function getCommand() : string
    {
        return trim($this->command);
    }

    public function getArguments() : ?string
    {
        return trim($this->arguments);
    }

    public function getDescription() : ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description) : void
    {
        $this->description = $description;
    }

    public function getMaxInstances() : int
    {
        return $this->maxInstances;
    }
}
