<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Domain\Abstract;

use PBaszak\UltraMapper\Mapper\Domain\Exception\Value\InvalidValueException;

trait MapDateTimeTrait
{
    /**
     * Return `DateTime` if the value represents `DateTime`. Otherwise throws exception.
     *
     * @param string|array{timezone?: ?string, date?: ?string}|object{timezone?: ?string, date?: ?string}|null $value
     *
     * @throws InvalidValueException
     */
    protected function mapDateTime(string $sourcePropertyPath, string $targetPropertyPath, mixed $value, bool $nullable = false): ?\DateTime
    {
        if ($value instanceof \DateTime) {
            return $value;
        }

        if ($nullable && $this->isNull($value)) {
            return null;
        }

        try {
            /** @var array<string, mixed> $value assumes that is $value `array`, if `null` then exception will be throwed */
            if (is_int($value)) {
                return new \DateTime('@'.$value);
            }
            if (is_string($value)) {
                return new \DateTime($value);
            }
            if (is_object($value)) {
                $value = (array) $value;
            }

            $timezone = $value['timezone'] ? $this->mapDateTimeZone($sourcePropertyPath, $targetPropertyPath, $value['timezone'], false) : null;
            $date = $value['date'] ?? 'now';

            return new \DateTime($date, $timezone);
        } catch (\Throwable) {
            throw new InvalidValueException($sourcePropertyPath, $targetPropertyPath, $value, 'Accepted values which are parsed as `DateTime` are strings or arrays with `date` key and optionally `timezone` key.', 'Use datetime string or array with `date` key and optionally `timezone` key as a value or use #[Callback] attribute to handle this case in Your way.', 8);
        }
    }

    /**
     * Return `DateTimeImmutable` if the value represents `DateTimeImmutable`. Otherwise throws exception.
     *
     * @param string|array{timezone?: ?string, date?: ?string}|object{timezone?: ?string, date?: ?string}|null $value
     *
     * @throws InvalidValueException
     */
    protected function mapDateTimeImmutable(string $sourcePropertyPath, string $targetPropertyPath, mixed $value, bool $nullable = false): ?\DateTimeImmutable
    {
        if ($value instanceof \DateTimeImmutable) {
            return $value;
        }

        if ($nullable && $this->isNull($value)) {
            return null;
        }

        try {
            /** @var \DateTime $dateTime */
            $dateTime = $this->mapDateTime($sourcePropertyPath, $targetPropertyPath, $value, false);
        } catch (\Throwable) {
            throw new InvalidValueException($sourcePropertyPath, $targetPropertyPath, $value, 'Accepted values which are parsed as `DateTimeImmutable` are strings or arrays with `date` key and optionally `timezone` key.', 'Use datetime string or array with `date` key and optionally `timezone` key as a value or use #[Callback] attribute to handle this case in Your way.', 9);
        }

        return \DateTimeImmutable::createFromMutable($dateTime);
    }

    /**
     * Return `DateTimeZone` if the value represents `DateTimeZone`. Otherwise throws exception.
     *
     * @param string|array{timezone?: ?string}|object{timezone?: ?string}|null $value
     *
     * @throws InvalidValueException
     */
    protected function mapDateTimeZone(string $sourcePropertyPath, string $targetPropertyPath, mixed $value, bool $nullable = false): ?\DateTimeZone
    {
        if ($value instanceof \DateTimeZone) {
            return $value;
        }

        if ($nullable && $this->isNull($value)) {
            return null;
        }

        try {
            /** @var array<string, mixed> $value assumes that is $value `array`, if `null` then exception will be throwed */
            if (is_string($value)) {
                return new \DateTimeZone($value);
            }

            if (is_object($value)) {
                $value = (array) $value;
            }

            return new \DateTimeZone($value['timezone']);
        } catch (\Throwable) {
            throw new InvalidValueException($sourcePropertyPath, $targetPropertyPath, $value, 'Accepted values which are parsed as `DateTimeZone` are strings or arrays with `timezone` key.', 'Use timezone string or array with `timezone` key as a value or use #[Callback] attribute to handle this case in Your way.', 10);
        }
    }

    /**
     * Return `DateInterval` if the value represents `DateInterval`. Otherwise throws exception.
     *
     * @param string|array{from_string?: bool, date_string?: string, y?: ?int, m?: ?int, d?: ?int, h?: ?int, i?: ?int, s?: ?int, f?: ?float, invert?: ?int, days?: mixed}|object{from_string?: bool, date_string?: string, y?: ?int, m?: ?int, d?: ?int, h?: ?int, i?: ?int, s?: ?int, f?: ?float, invert?: ?int, days?: mixed}|null $value
     *
     * @throws InvalidValueException
     */
    protected function mapDateInterval(string $sourcePropertyPath, string $targetPropertyPath, mixed $value, bool $nullable = false): ?\DateInterval
    {
        if ($value instanceof \DateInterval) {
            return $value;
        }

        if ($nullable && $this->isNull($value)) {
            return null;
        }

        try {
            if (is_string($value)) {
                try {
                    return new \DateInterval($value);
                } catch (\Exception) {
                    $interval = \DateInterval::createFromDateString($value);
                    if ($interval) {
                        return $interval;
                    }
                }
            }

            if (is_object($value)) {
                $value = (array) $value;
            }

            if (isset($value['from_string'])) {
                if ($value['from_string']) {
                    $interval = \DateInterval::createFromDateString($value['date_string']);
                } else {
                    $interval = new \DateInterval(sprintf(
                        'P%dY%dM%dDT%dH%dM%dS',
                        $value['y'] ?? 0,
                        $value['m'] ?? 0,
                        $value['d'] ?? 0,
                        $value['h'] ?? 0,
                        $value['i'] ?? 0,
                        $value['s'] ?? 0,
                    ));
                    $interval->f = $value['f'] ?? 0;
                    $interval->invert = $value['invert'] ?? 0;
                    $interval->days = $value['days'] ?? false;
                }

                if ($interval) {
                    return $interval;
                }
            }
        } catch (\Throwable) {
        }

        throw new InvalidValueException($sourcePropertyPath, $targetPropertyPath, $value, 'Accepted values which are parsed as `DateInterval` are strings or arrays with `from_string` key and optionally `date_string` key or arrays with `y`, `m`, `d`, `h`, `i`, `s`, `f`, `invert`, `days` keys.', 'Use date interval string or array with `from_string` key and optionally `date_string` key or array with `y`, `m`, `d`, `h`, `i`, `s`, `f`, `invert`, `days` keys as a value or use #[Callback] attribute to handle this case in Your way.', 11);
    }
}
