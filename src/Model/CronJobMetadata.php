<?php

namespace Shapecode\Bundle\CronBundle\Model;

use Symfony\Component\Console\Command\Command;

/**
 * Class CronJobMetadata
 *
 * @package Shapecode\Bundle\CronBundle\Model
 * @author  Nikita Loges
 */
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

    /**
     * @param string      $expression
     * @param string      $command
     * @param null|string $arguments
     */
    public function __construct(string $expression, string $command, ?string $arguments = null)
    {
        $this->expression = $expression;
        $this->command = $command;
        $this->arguments = $arguments;
    }

    /**
     * @param string      $expression
     * @param Command     $command
     * @param null|string $arguments
     *
     * @return CronJobMetadata
     */
    public static function createByCommand(string $expression, Command $command, ?string $arguments = null)
    {
        $meta = new static($expression, $command->getName(), $arguments);
        $meta->setDescription($command->getDescription());

        return $meta;
    }

    /**
     * @return string
     */
    public function getExpression(): string
    {
        return $this->expression;
    }

    /**
     * @return string
     */
    public function getClearedExpression(): string
    {
        $expression = $this->getExpression();
        $expression = str_replace('\\', '', $expression);

        return $expression;
    }

    /**
     * @return string
     */
    public function getFullCommand()
    {
        $arguments = '';

        if (!empty($this->getArguments())) {
            $arguments = ' ' . $this->getArguments();
        }

        return trim($this->getCommand() . $arguments);
    }

    /**
     * @return string
     */
    public function getCommand(): string
    {
        return trim($this->command);
    }

    /**
     * @return null|string
     */
    public function getArguments(): ?string
    {
        return trim($this->arguments);
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }
}
