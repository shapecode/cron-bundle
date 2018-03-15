<?php

namespace Shapecode\Bundle\CronBundle\CronJob;

use Shapecode\Bundle\CronBundle\Event\GenericCleanUpEvent;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class GenericCleanUpHourlyCommand
 *
 * @package Shapecode\Bundle\CronBundle\CronJob
 * @author  Nikita Loges
 */
class GenericCleanUpHourlyCommand extends Command
{

    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @inheritDoc
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        parent::__construct();

        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('shapecode:cron:generic-cleanup:hourly');
    }

    /**
     * @inheritdoc
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        // start cron
        $event = new GenericCleanUpEvent($input, $output);
        $this->eventDispatcher->dispatch(GenericCleanUpEvent::HOURLY_START, $event);

        // process cron
        $event = new GenericCleanUpEvent($input, $output);
        $this->eventDispatcher->dispatch(GenericCleanUpEvent::HOURLY_PROCESS, $event);

        // end cron
        $event = new GenericCleanUpEvent($input, $output);
        $this->eventDispatcher->dispatch(GenericCleanUpEvent::HOURLY_END, $event);
    }
}
