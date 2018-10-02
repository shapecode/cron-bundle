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

    /** @var string */
    protected $description;

    /** @var string */
    protected $arguments;

    /** @var int */
    protected $maxInstances;

    /**
     * @param      $expression
     * @param      $command
     * @param null $arguments
     * @param int  $maxInstances
     */
    public function __construct($expression, $command, $arguments = null, $maxInstances = 1)
    {
        $this->expression = $expression;
        $this->command = $command;
        $this->arguments = $arguments;
        $this->maxInstances = $maxInstances;
    }

    /**
     * @param         $expression
     * @param Command $command
     * @param         $arguments
     * @param int     $maxInstances
     *
     * @return static
     */
    public static function createByCommand($expression, Command $command, $arguments = null, $maxInstances = 1)
    {
        $meta = new static($expression, $command->getName(), $arguments, $maxInstances);
        $meta->setDescription($command->getDescription());

        return $meta;
    }

    /**
     * @return string
     */
    public function getExpression()
    {
        return $this->expression;
    }

    /**
     * @return string
     */
    public function getClearedExpression()
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
    public function getCommand()
    {
        return trim($this->command);
    }

    /**
     * @return string
     */
    public function getArguments()
    {
        return trim($this->arguments);
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return int
     */
    public function getMaxInstances()
    {
        return $this->maxInstances;
    }
}
