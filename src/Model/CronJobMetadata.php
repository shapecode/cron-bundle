<?php

namespace Shapecode\Bundle\CronBundle\Model;

use Symfony\Component\Console\Command\Command;

/**
 * Class CronJobMetadata
 *
 * @package Shapecode\Bundle\CronBundle\Model
 * @author  Nikita Loges
 * @company tenolo GbR
 */
class CronJobMetadata
{

    /** @var string */
    protected $expression;

    /** @var Command */
    protected $command;

    /**
     * @param Command $command
     * @param string  $expression
     */
    public function __construct(Command $command, $expression)
    {
        $this->expression = $expression;
        $this->command = $command;
    }

    /**
     * @return Command
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * @return string
     */
    public function getExpression()
    {
        return $this->expression;
    }
}
