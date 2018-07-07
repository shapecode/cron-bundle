<?php

namespace Shapecode\Bundle\CronBundle\Event;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class GenericCleanUpEvent
 *
 * @package Shapecode\Bundle\CronBundle\Event
 * @author  Nikita Loges
 */
class GenericCleanUpEvent extends Event
{

    public const HOURLY_START = 'shapecode_cron.generic_cleanup.hourly.start';
    public const HOURLY_PROCESS = 'shapecode_cron.generic_cleanup.hourly.process';
    public const HOURLY_END = 'shapecode_cron.generic_cleanup.hourly.end';

    public const DAILY_START = 'shapecode_cron.generic_cleanup.daily.start';
    public const DAILY_PROCESS = 'shapecode_cron.generic_cleanup.daily.process';
    public const DAILY_END = 'shapecode_cron.generic_cleanup.daily.end';

    /** @var InputInterface */
    protected $input;

    /** @var OutputInterface */
    protected $output;

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
    }

    /**
     * @return InputInterface
     */
    public function getInput(): InputInterface
    {
        return $this->input;
    }

    /**
     * @return OutputInterface
     */
    public function getOutput(): OutputInterface
    {
        return $this->output;
    }
}
