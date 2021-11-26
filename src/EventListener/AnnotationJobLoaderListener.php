<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\EventListener;

use Doctrine\Common\Annotations\Reader;
use ReflectionClass;
use Shapecode\Bundle\CronBundle\Annotation\CronJob;
use Shapecode\Bundle\CronBundle\Event\LoadJobsEvent;
use Shapecode\Bundle\CronBundle\Model\CronJobMetadata;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelInterface;

use function assert;
use function is_string;

final class AnnotationJobLoaderListener implements EventSubscriberInterface
{
    private Application $application;

    public function __construct(
        KernelInterface $kernel,
        private readonly Reader $reader
    ) {
        $this->application = new Application($kernel);
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents(): array
    {
        return [LoadJobsEvent::NAME => 'onLoadJobs'];
    }

    public function onLoadJobs(LoadJobsEvent $event): void
    {
        foreach ($this->application->all() as $command) {
            // Check for an @CronJob annotation
            $reflectionClass = new ReflectionClass($command);

            foreach ($this->reader->getClassAnnotations($reflectionClass) as $annotation) {
                if (! ($annotation instanceof CronJob)) {
                    continue;
                }

                $arguments    = $annotation->arguments;
                $maxInstances = $annotation->maxInstances;
                $schedule     = $annotation->value;
                assert(is_string($schedule));

                $meta = CronJobMetadata::createByCommand($schedule, $command, $arguments, $maxInstances);
                $event->addJob($meta);
            }
        }
    }
}
