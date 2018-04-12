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

    /**
     * @param      $expression
     * @param      $command
     * @param null $arguments
     */
    public function __construct($expression, $command, $arguments = null)
    {
        $this->expression = $expression;
        $this->command = $command;
        $this->arguments = $arguments;
    }

    /**
     * @param         $expression
     * @param Command $command
     * @param         $arguments
     *
     * @return static
     */
    public static function createByCommand($expression, Command $command, $arguments = null)
    {
        $meta = new static($expression, $command->getName(), $arguments);
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
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * @return string
     */
    public function getArguments()
    {
        return $this->arguments;
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
}
