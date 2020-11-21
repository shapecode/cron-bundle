<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Tests\Command;

use PHPUnit\Framework\TestCase;
use Shapecode\Bundle\CronBundle\Command\CronPruneLogsCommand;
use Shapecode\Bundle\CronBundle\Service\CronJobResultServiceInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;

final class CronPruneLogsCommandTest extends TestCase
{
    public function testRun(): void
    {
        $prune   = $this->createMock(CronJobResultServiceInterface::class);
        $command = new CronPruneLogsCommand($prune);

        $input  = $this->createMock(InputInterface::class);
        $output = new BufferedOutput();

        $command->run($input, $output);

        $expected = "Cleaning logs for all cron jobs\nLogs cleaned successfully\n";

        self::assertEquals($expected, $output->fetch());
        self::assertEquals('shapecode:cron:result:prune', $command->getName());
    }
}
