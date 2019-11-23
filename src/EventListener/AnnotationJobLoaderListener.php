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

class AnnotationJobLoaderListener implements EventSubscriberInterface
{
    /** @var KernelInterface */
    protected $kernel;

    /** @var Application */
    protected $application;

    /** @var Reader */
    protected $reader;

    public function __construct(KernelInterface $kernel, Reader $reader)
    {
        $this->kernel      = $kernel;
        $this->application = new Application($kernel);
        $this->reader      = $reader;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents() : array
    {
        return [
            LoadJobsEvent::NAME => 'onLoadJobs',
        ];
    }

    public function onLoadJobs(LoadJobsEvent $event) : void
    {
        foreach ($this->application->all() as $command) {
            // Check for an @CronJob annotation
            $reflClass = new ReflectionClass($command);

            foreach ($this->reader->getClassAnnotations($reflClass) as $annotation) {
                if (! ($annotation instanceof CronJob)) {
                    continue;
                }

                $schedule     = $annotation->value;
                $arguments    = $annotation->getArguments();
                $maxInstances = $annotation->getMaxInstances();

                $meta = CronJobMetadata::createByCommand($schedule, $command, $arguments, $maxInstances);
                $event->addJob($meta);
            }
        }
    }
}
