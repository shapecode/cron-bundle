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

    /** @var string|null */
    protected $arguments;

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return null|string
     */
    public function getArguments(): ?string
    {
        return $this->arguments;
    }

}
