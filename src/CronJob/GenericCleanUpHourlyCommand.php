<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\CronJob;

use Shapecode\Bundle\CronBundle\Event\GenericCleanUpEvent;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class GenericCleanUpHourlyCommand extends Command
{
    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        parent::__construct();

        $this->eventDispatcher = $eventDispatcher;
    }

    protected function configure() : void
    {
        $this->setName('shapecode:cron:generic-cleanup:hourly');
    }

    public function execute(InputInterface $input, OutputInterface $output) : int
    {
        // start cron
        $event = new GenericCleanUpEvent($input, $output);
        $this->eventDispatcher->dispatch($event, GenericCleanUpEvent::HOURLY_START);

        // process cron
        $event = new GenericCleanUpEvent($input, $output);
        $this->eventDispatcher->dispatch($event, GenericCleanUpEvent::HOURLY_PROCESS);

        // end cron
        $event = new GenericCleanUpEvent($input, $output);
        $this->eventDispatcher->dispatch($event, GenericCleanUpEvent::HOURLY_END);

        return 0;
    }
}
