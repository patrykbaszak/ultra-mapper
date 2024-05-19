<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Domain\Abstract;

use PBaszak\UltraMapper\Mapper\Domain\Exception\Value\InvalidValueException;

/**
 * Class AbstractMapper is a base class for all mappers.
 * Includes methods for mapping values to basic types.
 */
abstract class AbstractMapper
{
    use MapDateTimeTrait;

    private const MESSAGE_ACCEPTED_NULL_VALUES = 'Accepted values which are parsed as `null` are: `null`, `0`, `""`, `"null"`, `"NULL"`, `"Null"`.';
    private const MESSAGE_ACCEPTED_TRUE_VALUES = 'Accepted values which are parsed as `true` are: `true`, `1`, `"true"`, `"TRUE"`, `"True"`.';
    private const MESSAGE_ACCEPTED_FALSE_VALUES = 'Accepted values which are parsed as `false` are: `false`, `0`, `"false"`, `"FALSE"`, `"False"`.';
    private const MESSAGE_ACCEPTED_BOOL_VALUES = 'Accepted values which are parsed as `bool` are: `true`, `1`, `"true"`, `"TRUE"`, `"True"`, `false`, `0`, `"false"`, `"FALSE"`, `"False"`.';

    public bool $booleansAsIntegersWhenParsingToString = false;

    /**
     * Return `null` if the value represents `null`. Otherwise throws exception.
     *
     * @param bool $nullable - not used but required for compatibility with other methods
     *
     * @throws InvalidValueException
     */
    protected function mapNull(string $sourcePropertyPath, string $targetPropertyPath, mixed $value, bool $nullable = false): null
    {
        if ($this->isNull($value)) {
            return null;
        }

        throw new InvalidValueException($sourcePropertyPath, $targetPropertyPath, $value, self::MESSAGE_ACCEPTED_NULL_VALUES, 'Use `null` as a value or use #[Callback] attribute to handle this case in Your way.', 1);
    }

    /**
     * Return `true` if the value represents `true`. Otherwise throws exception.
     *
     * @throws InvalidValueException
     */
    protected function mapTrue(string $sourcePropertyPath, string $targetPropertyPath, mixed $value, bool $nullable = false): ?true
    {
        if ($this->isTrue($value)) {
            return true;
        }

        if ($nullable && $this->isNull($value)) {
            return null;
        }

        throw new InvalidValueException($sourcePropertyPath, $targetPropertyPath, $value, self::MESSAGE_ACCEPTED_TRUE_VALUES.($nullable ? ' '.self::MESSAGE_ACCEPTED_NULL_VALUES : ''), 'Use `true` as a value or use #[Callback] attribute to handle this case in Your way.', 2);
    }

    /**
     * Return `false` if the value represents `false`. Otherwise throws exception.
     *
     * @throws InvalidValueException
     */
    protected function mapFalse(string $sourcePropertyPath, string $targetPropertyPath, mixed $value, bool $nullable = false): ?false
    {
        if ($this->isFalse($value)) {
            return false;
        }

        if ($nullable && $this->isNull($value)) {
            return null;
        }

        throw new InvalidValueException($sourcePropertyPath, $targetPropertyPath, $value, self::MESSAGE_ACCEPTED_FALSE_VALUES.($nullable ? ' '.self::MESSAGE_ACCEPTED_NULL_VALUES : ''), 'Use `false` as a value or use #[Callback] attribute to handle this case in Your way.', 3);
    }

    /**
     * Return `bool` if the value represents `bool`. Otherwise throws exception.
     *
     * @throws InvalidValueException
     */
    protected function mapBool(string $sourcePropertyPath, string $targetPropertyPath, mixed $value, bool $nullable = false): ?bool
    {
        if ($this->isBool($value)) {
            return true;
        }

        if ($nullable && $this->isNull($value)) {
            return null;
        }

        throw new InvalidValueException($sourcePropertyPath, $targetPropertyPath, $value, self::MESSAGE_ACCEPTED_BOOL_VALUES.($nullable ? ' '.self::MESSAGE_ACCEPTED_NULL_VALUES : ''), 'Use `true` as a value or use #[Callback] attribute to handle this case in Your way.', 4);
    }

    /**
     * Return `int` if the value represents `int`. Otherwise throws exception.
     *
     * @throws InvalidValueException
     */
    protected function mapInt(string $sourcePropertyPath, string $targetPropertyPath, mixed $value, bool $nullable = false): ?int
    {
        if (is_int($value)) {
            return $value;
        }

        // if the value is a numeric string which is possible to parse to integer
        // if the numeric is float it will be thrown as an exception
        if (is_numeric($value) && ($i = (int) $value) == $value) {
            return $i;
        }

        if ($nullable && $this->isNull($value)) {
            return null;
        }

        throw new InvalidValueException($sourcePropertyPath, $targetPropertyPath, $value, 'Accepted values which are parsed as `int` are integers and numeric strings which is possible to parse to integer. Floats are not casted to integers if not equals.', 'Use integer as a value or use #[Callback] attribute to handle this case in Your way.', 5);
    }

    /**
     * Return `float` if the value represents `float`. Otherwise throws exception.
     *
     * @throws InvalidValueException
     */
    protected function mapFloat(string $sourcePropertyPath, string $targetPropertyPath, mixed $value, bool $nullable = false): ?float
    {
        if (is_float($value)) {
            return $value;
        }

        // if the value is a numeric string which is possible to parse to float
        if (is_numeric($value) && ($f = (float) $value) == $value) {
            return $f;
        }

        if ($nullable && $this->isNull($value)) {
            return null;
        }

        throw new InvalidValueException($sourcePropertyPath, $targetPropertyPath, $value, 'Accepted values which are parsed as `float` are floats and numeric strings which is possible to parse to float. Integers are casted to float.', 'Use float as a value or use #[Callback] attribute to handle this case in Your way.', 6);
    }

    /**
     * Return `string` if the value represents `string`. Otherwise throws exception.
     *
     * @throws InvalidValueException
     */
    protected function mapString(string $sourcePropertyPath, string $targetPropertyPath, mixed $value, bool $nullable = false): ?string
    {
        if ($this->isNull($value)) {
            if ($nullable) {
                return null;
            }

            return 'null';
        }

        if (is_string($value)) {
            return $value;
        }

        if ($this->booleansAsIntegersWhenParsingToString) {
            if ($this->isTrue($value)) {
                return '1';
            }
            if ($this->isFalse($value)) {
                return '0';
            }
        } else {
            if ($this->isTrue($value)) {
                return 'true';
            }
            if ($this->isFalse($value)) {
                return 'false';
            }
        }

        try {
            return (string) $value;
        } catch (\Throwable) {
            throw new InvalidValueException($sourcePropertyPath, $targetPropertyPath, $value, 'Accepted values which are parsed as `string` are strings or stringable resources.', 'Use string as a value or use #[Callback] attribute to handle this case in Your way.', 7);
        }
    }

    protected function isNull(mixed $value): bool
    {
        return match ($value) {
            null, 0, 'null', 'NULL', 'Null' => true,
            default => false,
        };
    }

    protected function isTrue(mixed $value): bool
    {
        return match ($value) {
            true, 1, 't', 'true', 'TRUE', 'True' => true,
            default => false,
        };
    }

    protected function isFalse(mixed $value): bool
    {
        return match ($value) {
            false, 0, 'f', 'false', 'FALSE', 'False' => true,
            default => false,
        };
    }

    protected function isBool(mixed $value): bool
    {
        return $this->isTrue($value) || $this->isFalse($value);
    }
}
