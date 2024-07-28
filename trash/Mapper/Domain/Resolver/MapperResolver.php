<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Domain\Resolver;

use PBaszak\UltraMapper\Mapper\Domain\Contract\ClassMapperInterface;

class MapperResolver
{
    /** @var array<string, ClassMapperInterface> */
    protected array $mappers = [];

    /**
     * The MapperResolver:
     *  - finds the appropriate mapper for the given arguments.
     *  - saves the mapper in the mappers directory.
     *
     * @param string $mappersDirectory The directory where mappers are stored. The default value assumes
     *                                 that the library is installed via Composer and the mappers are stored
     *                                 in the var/ultra-mapper/mappers/ directory.
     * @param string $mappersNamespace The namespace of the mappers. Only for protection against collisions.
     */
    public function __construct(
        protected string $mappersDirectory = __DIR__.'/../../../../../../../var/ultra-mapper/mappers/',
        protected string $mappersNamespace = 'PBaszak\\UltraMapper\\Mappers\\',
    ) {
    }

    /**
     * Generates a short class name for the mapper based on the given arguments.
     *
     * @param mixed ...$arguments The arguments to generate the short class name.
     *
     * @return string the short class name of the mapper
     */
    public function getMapperShortClassName(mixed ...$arguments): string
    {
        if (empty($arguments)) {
            throw new \InvalidArgumentException('At least one argument is required.');
        }

        $hashAlgo = array_intersect(
            ['xxh3', 'xxh128', 'crc32c', 'xxh64', 'murmur3f', 'md5'],
            hash_algos(),
        )[0];

        return hash($hashAlgo, serialize($arguments));
    }

    /**
     * Resolves the mapper by its short class name.
     *
     * @param string $mapperShortClassName The short class name of the mapper.
     *                                     Call getMapperShortClassName() to get it.
     *
     * @return ClassMapperInterface|null the mapper if it exists, null otherwise
     */
    public function resolve(string $mapperShortClassName): ?ClassMapperInterface
    {
        if (array_key_exists($mapperShortClassName, $this->mappers)) {
            return $this->mappers[$mapperShortClassName];
        }

        $mapperClassName = $this->mappersNamespace.$mapperShortClassName;
        $mapperFilePath = $this->mappersDirectory.$mapperShortClassName.'.php';

        if (file_exists($mapperFilePath)) {
            require_once $mapperFilePath;
            $this->mappers[$mapperShortClassName] = new $mapperClassName();

            return $this->mappers[$mapperShortClassName];
        }

        return null;
    }

    /**
     * Saves the mapper in the mappers directory. If the mapper already exists, it will be overwritten.
     * If the directory does not exist, it will be created.
     *
     * @param string $mapperShortClassName the short class name of the mapper
     * @param string $mapperFileBody       the body of the mapper file
     *
     * @throws \RuntimeException if the mapper could not be saved
     */
    public function save(string $mapperShortClassName, string $mapperFileBody): void
    {
        unset($this->mappers[$mapperShortClassName]);
        $mapperFilePath = $this->mappersDirectory.$mapperShortClassName.'.php';

        if (!is_readable($this->mappersDirectory)) {
            mkdir($this->mappersDirectory, 0777, true);
        }

        $result = file_put_contents($mapperFilePath, $mapperFileBody, LOCK_EX);

        if (false === $result) {
            throw new \RuntimeException('Failed to save the mapper.');
        }
    }
}
