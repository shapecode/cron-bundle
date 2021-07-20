<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Tests\Annotation\data;

#[CronJob('ls', arguments: '-l', maxInstances: 3)]
class TestAttributeModel
{
}
