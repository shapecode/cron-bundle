<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 */
final class CronJob extends Annotation
{
    /** @var string|null */
    private $arguments;

    /** @var int */
    public $maxInstances = 1;

    public function getValue() : string
    {
        return $this->value;
    }

    public function getArguments() : ?string
    {
        return $this->arguments;
    }

    public function getMaxInstances() : int
    {
        return $this->maxInstances;
    }
}
