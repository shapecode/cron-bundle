<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\EventListener;

use Doctrine\Common\Annotations\Reader;
use ReflectionClass;
use Shapecode\Bundle\CronBundle\Annotation\CronJob;
use Shapecode\Bundle\CronBundle\Event\LoadJobsEvent;
use Shapecode\Bundle\CronBundle\Model\CronJobMetadata;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\KernelInterface;

use function assert;
use function is_string;

#[AsEventListener]
final class AnnotationJobLoaderListener
{
    private readonly Application $application;

    public function __construct(
        KernelInterface $kernel,
        private readonly Reader $reader,
    ) {
        $this->application = new Application($kernel);
    }

    public function __invoke(LoadJobsEvent $event): void
    {
        foreach ($this->application->all() as $command) {
            // Check for an @CronJob annotation
            $reflectionClass = new ReflectionClass($command);

            $annotations = $this->reader->getClassAnnotations($reflectionClass);

            foreach ($annotations as $annotation) {
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
