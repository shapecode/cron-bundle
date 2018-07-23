<?php


namespace Shapecode\Bundle\CronBundle\Tests\Manager;


use Doctrine\Common\Annotations\Reader;
use Shapecode\Bundle\CronBundle\Annotation\CronJob;
use Shapecode\Bundle\CronBundle\Manager\CronJobManager;
use Shapecode\Bundle\CronBundle\Model\CronJobMetadata;
use Shapecode\Bundle\CronBundle\Tests\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;

class CronJobManagerTest extends TestCase
{

    /**
     * @var \Shapecode\Bundle\CronBundle\Manager\CronJobManager|\Mockery\Mock
     */
    protected $cronJobManagerMock;

    protected function setUp()
    {
        parent::setUp();

        $this->cronJobManagerMock = \Mockery::mock(CronJobManager::class)->makePartial();
    }

    public function testGetApplicationJobs()
    {
        $commandMock = \Mockery::mock(Command::class)->makePartial();
        $applicationMock = \Mockery::mock(Application::class)
                                   ->shouldReceive('all')
                                   ->andReturn([$commandMock])
                                   ->getMock();
        $expression = "* * * * *";
        $cronJobAnnotation = new CronJob(['value' => $expression]);

        $readerMock = \Mockery::mock(Reader::class)
                              ->shouldReceive('getClassAnnotations')
                              ->andReturn([$cronJobAnnotation])
                              ->getMock();

        $this->cronJobManagerMock->shouldReceive('getApplication')
                                 ->andReturn($applicationMock);

        $this->cronJobManagerMock->shouldReceive('getReader')
                                 ->andReturn($readerMock);

        $jobs = $this->cronJobManagerMock->getJobs();

        $this->assertCount(1, $jobs);

        $this->assertInstanceOf(CronJobMetadata::class, $jobs[0]);
        $this->assertEquals($commandMock, $jobs[0]->getCommand());
        $this->assertEquals($expression, $jobs[0]->getExpression());

        // Run second time to assert the same result.
        $jobs = $this->cronJobManagerMock->getJobs();
        $this->assertCount(1, $jobs);
        $this->assertInstanceOf(CronJobMetadata::class, $jobs[0]);
        $this->assertEquals($commandMock, $jobs[0]->getCommand());
        $this->assertEquals($expression, $jobs[0]->getExpression());
    }

}