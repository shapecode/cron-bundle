<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Shapecode\Bundle\CronBundle\Command\CronJobEditCommand;
use Shapecode\Bundle\CronBundle\Command\CronProcessCommand;
use Shapecode\Bundle\CronBundle\Command\CronPruneLogsCommand;
use Shapecode\Bundle\CronBundle\Command\CronRunCommand;
use Shapecode\Bundle\CronBundle\Command\CronScanCommand;
use Shapecode\Bundle\CronBundle\Command\CronStatusCommand;
use Shapecode\Bundle\CronBundle\Controller\CronJobController;
use Shapecode\Bundle\CronBundle\CronJob\GenericCleanUpDailyCommand;
use Shapecode\Bundle\CronBundle\CronJob\GenericCleanUpHourlyCommand;
use Shapecode\Bundle\CronBundle\DependencyInjection\ShapecodeCronExtension;
use Shapecode\Bundle\CronBundle\EventListener\AnnotationJobLoaderListener;
use Shapecode\Bundle\CronBundle\EventListener\EntitySubscriber;
use Shapecode\Bundle\CronBundle\EventListener\ResultAutoPruneListener;
use Shapecode\Bundle\CronBundle\EventListener\ServiceJobLoaderListener;
use Shapecode\Bundle\CronBundle\Manager\CronJobManager;
use Shapecode\Bundle\CronBundle\Manager\CronJobManagerInterface;
use Shapecode\Bundle\CronBundle\Service\CommandHelper;
use Shapecode\Bundle\CronBundle\Service\CronJobResultService;
use Shapecode\Bundle\CronBundle\Service\CronJobResultServiceInterface;

class ShapecodeCronExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @inheritDoc
     */
    protected function getContainerExtensions() : array
    {
        return [
            new ShapecodeCronExtension(),
        ];
    }

    public function testCommands() : void
    {
        $this->load();

        $this->assertContainerBuilderHasService(CronJobEditCommand::class);
        $this->assertContainerBuilderHasService(CronProcessCommand::class);
        $this->assertContainerBuilderHasService(CronPruneLogsCommand::class);
        $this->assertContainerBuilderHasService(CronRunCommand::class);
        $this->assertContainerBuilderHasService(CronScanCommand::class);
        $this->assertContainerBuilderHasService(CronStatusCommand::class);
    }

    public function testControllers() : void
    {
        $this->load();

        $this->assertContainerBuilderHasService(CronJobController::class);
    }

    public function testCronJobs() : void
    {
        $this->load();

        $this->assertContainerBuilderHasService(GenericCleanUpDailyCommand::class);
        $this->assertContainerBuilderHasService(GenericCleanUpHourlyCommand::class);
    }

    public function testEventListeners() : void
    {
        $this->load();

        $this->assertContainerBuilderHasService(AnnotationJobLoaderListener::class);
        $this->assertContainerBuilderHasService(EntitySubscriber::class);
        $this->assertContainerBuilderHasService(ResultAutoPruneListener::class);
        $this->assertContainerBuilderHasService(ServiceJobLoaderListener::class);
    }

    public function testManagers() : void
    {
        $this->load();

        $this->assertContainerBuilderHasService(CronJobManager::class);
        $this->assertContainerBuilderHasAlias(CronJobManagerInterface::class);
    }

    public function testServices() : void
    {
        $this->load();

        $this->assertContainerBuilderHasService(CommandHelper::class);
        $this->assertContainerBuilderHasService(CronJobResultService::class);
        $this->assertContainerBuilderHasAlias(CronJobResultServiceInterface::class);
    }
}
