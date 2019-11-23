<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Tests\Command;

use Mockery;
use Mockery\Mock;
use Shapecode\Bundle\CronBundle\Command\CronRunCommand;
use Shapecode\Bundle\CronBundle\Tests\TestCase;
use Symfony\Component\Process\Process;

class CronRunCommandTest extends TestCase
{
    /** @var CronRunCommand|Mock */
    protected $commandMock;

    protected function setUp() : void
    {
        parent::setUp();

        $this->commandMock = Mockery::mock(CronRunCommand::class)->makePartial();
    }

    public function testWaitProcesses() : void
    {
        $processes[] = Mockery::mock(Process::class)
                               ->shouldReceive('isRunning')
                               ->times(4)
                               ->andReturnValues([true, true, false])
                               ->getMock();

        $processes[] = Mockery::mock(Process::class)
                               ->shouldReceive('isRunning')
                               ->times(2)
                               ->andReturnValues([true, false])
                               ->getMock();

        $this->commandMock->waitProcesses($processes);
        $this->assertTrue(true);
    }
}
