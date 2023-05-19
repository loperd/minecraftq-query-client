<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Tests\Helper;

use ReflectionClass;

class ReflectionHelper
{
    /**
     * @throws \ReflectionException
     */
    public static function setProperties(ReflectionClass $reflection, object $object, array $properties): void
    {
        foreach ($properties as $name => $value) {
            $reflection_property = $reflection->getProperty($name);
            $reflection_property->setValue($object, $value);
        }
    }
}
