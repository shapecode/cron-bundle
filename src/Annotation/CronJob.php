<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Annotation;

use Attribute;
use BadMethodCallException;
use Doctrine\Common\Annotations\Annotation;

use function Attribute;
use function is_array;
use function is_string;

/**
 * @Annotation
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class CronJob
{
    public string $value;

    public ?string $arguments = null;

    public int $maxInstances = 1;

    /**
     * @param string|mixed[] $data
     */
    public function __construct($data, ?string $arguments = null, ?int $maxInstances = null)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $this->$key = $value;
            }
        } elseif (is_string($data)) {
            $this->value        = $data;
            $this->arguments    = $arguments ?? $this->arguments;
            $this->maxInstances = $maxInstances ?? $this->maxInstances;
        } else {
            throw new BadMethodCallException('$data must be an array or string');
        }
    }
}
