<?php

namespace Shapecode\Bundle\CronBundle;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class ShapecodeCronBundle
 * @package Shapecode\Bundle\CronBundle
 * @author Nikita Loges
 */
class ShapecodeCronBundle extends Bundle
{
    /**
     * @{@inheritdoc}
     */
    public function boot()
    {
        // register doctrine annotation
        AnnotationRegistry::registerFile(__DIR__ . "/Annotation/CronJob.php");
    }
}
