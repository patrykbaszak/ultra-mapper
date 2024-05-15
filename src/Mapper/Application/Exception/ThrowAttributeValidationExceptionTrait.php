<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Application\Exception;

trait ThrowAttributeValidationExceptionTrait
{
    /**
     * @throws AttributeException
     */
    protected function throwAttributeValidationException(string $message, int $code, \ReflectionProperty|\ReflectionClass $reflection): void
    {
        $class = $reflection instanceof \ReflectionClass ? $reflection->getName() : $reflection->getDeclaringClass()->getName();
        $property = $reflection instanceof \ReflectionClass ? null : $reflection->getName();

        throw new AttributeValidationException(sprintf('The %s attribute on %s%s is invalid. %s', (new \ReflectionClass($this))->getShortName(), $class, $property ? '::'.$property : '', $message), $code);
    }
}
