<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Service;

use ReflectionClass;

class AttributeReader
{
    /**
     * @return object[]
     */
    public function getClassAttributes(ReflectionClass $class, ?string $filterClass = null): array
    {
        $attribs = [];
        foreach ($class->getAttributes($filterClass) as $attribute) {
            $attribs[] = $attribute->newInstance();
        }

        return $attribs;
    }
}
