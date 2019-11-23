<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\CronJob;

use RuntimeException;
use Shapecode\Bundle\CronBundle\Event\GenericCleanUpEvent;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\LegacyEventDispatcherProxy;
use Symfony\Component\HttpKernel\Kernel;

class GenericCleanUpDailyCommand extends Command
{
    /** @var LegacyEventDispatcherProxy|EventDispatcherInterface */
    protected $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        parent::__construct();

        if (Kernel::VERSION_ID > 40300) {
            $legacy = LegacyEventDispatcherProxy::decorate($eventDispatcher);

            if ($legacy === null) {
                throw new RuntimeException('there is not event dispatcher provided');
            }

            $eventDispatcher = $legacy;
        }

        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @inheritDoc
     */
    protected function configure() : void
    {
        $this->setName('shapecode:cron:generic-cleanup:daily');
    }

    /**
     * @inheritdoc
     */
    public function execute(InputInterface $input, OutputInterface $output) : int
    {
        // start cron
        $event = new GenericCleanUpEvent($input, $output);
        $this->eventDispatcher->dispatch(GenericCleanUpEvent::DAILY_START, $event);

        // process cron
        $event = new GenericCleanUpEvent($input, $output);
        $this->eventDispatcher->dispatch(GenericCleanUpEvent::DAILY_PROCESS, $event);

        // end cron
        $event = new GenericCleanUpEvent($input, $output);
        $this->eventDispatcher->dispatch(GenericCleanUpEvent::DAILY_END, $event);

        return 0;
    }
}
