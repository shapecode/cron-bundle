<?php

namespace Shapecode\Bundle\CronBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class CronJob
 *
 * @package Shapecode\Bundle\CronBundle\Annotation
 * @author  Nikita Loges
 *
 * @Annotation
 */
class CronJob extends Annotation
{
    public $value;
}
