<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Blueprint\Application\Model\Assets;

use PBaszak\UltraMapper\Shared\Infrastructure\Normalization\Normalizable;

class AssetsAggregate implements \ArrayAccess, \IteratorAggregate, Normalizable
{
    public function __construct(
        public object $root,
        /** @var array<string, Normalizable|mixed[]> */
        public array $assets,
    ) {
    }

    public function offsetExists(mixed $offset): bool
    {
        if (!is_string($offset)) {
            throw new \InvalidArgumentException('Aggregate key must be a string');
        }

        return isset($this->assets[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        if (!is_string($offset)) {
            throw new \InvalidArgumentException('Aggregate key must be a string');
        }

        return $this->assets[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (!is_string($offset)) {
            throw new \InvalidArgumentException('Aggregate key must be a string');
        }

        $this->assets[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        if (!is_string($offset)) {
            throw new \InvalidArgumentException('Aggregate key must be a string');
        }

        unset($this->assets[$offset]);
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->assets);
    }

    public function normalize(): array
    {
        return array_map(
            fn (mixed $asset) => $asset instanceof Normalizable
                ? $asset->normalize()
                : (
                    is_iterable($asset)
                    ? array_map(
                        fn (mixed $item) => $item instanceof Normalizable
                            ? $item->normalize()
                            : $item,
                        (array) $asset
                    )
                    : $asset
                ),
            $this->assets
        );
    }

    public function __clone(): void
    {
        $this->assets = array_map(
            fn (mixed $asset) => is_object($asset) ? clone $asset : $asset,
            $this->assets
        );
    }
}
