<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Tests\Annotation\data;

use Shapecode\Bundle\CronBundle\Annotation\CronJob;

/**
 * @CronJob("ls", arguments="-l", maxInstances=3)
 */
final class TestModel
{
}
