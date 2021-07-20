<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\EventListener;

use Doctrine\Common\Annotations\Reader;
use ReflectionClass;
use Shapecode\Bundle\CronBundle\Annotation\CronJob;
use Shapecode\Bundle\CronBundle\Event\LoadJobsEvent;
use Shapecode\Bundle\CronBundle\Model\CronJobMetadata;
use Shapecode\Bundle\CronBundle\Service\AttributeReader;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelInterface;

use function array_merge;
use function phpversion;
use function version_compare;

final class AnnotationJobLoaderListener implements EventSubscriberInterface
{
    private Application $application;

    private Reader $reader;

    private AttributeReader $attributeReader;

    public function __construct(KernelInterface $kernel, Reader $reader, AttributeReader $attributeReader)
    {
        $this->application     = new Application($kernel);
        $this->reader          = $reader;
        $this->attributeReader = $attributeReader;
    }

    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            LoadJobsEvent::NAME => 'onLoadJobs',
        ];
    }

    public function onLoadJobs(LoadJobsEvent $event): void
    {
        foreach ($this->application->all() as $command) {
            // Check for an @CronJob annotation
            $reflClass = new ReflectionClass($command);

            $annotations = $this->reader->getClassAnnotations($reflClass);

            if (version_compare(phpversion(), '8.0.0', '>=')) {
                $annotations = array_merge(
                    $annotations,
                    $this->attributeReader->getClassAttributes($reflClass, CronJob::class)
                );
            }

            foreach ($annotations as $annotation) {
                if (! ($annotation instanceof CronJob)) {
                    continue;
                }

                $schedule     = $annotation->value;
                $arguments    = $annotation->arguments;
                $maxInstances = $annotation->maxInstances;

                $meta = CronJobMetadata::createByCommand($schedule, $command, $arguments, $maxInstances);
                $event->addJob($meta);
            }
        }
    }
}
