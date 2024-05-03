<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Blueprint\Application\Serializer;

use PBaszak\UltraMapper\Blueprint\Domain\Entity\Blueprint;
use Symfony\Component\Yaml\Yaml;

/**
 * The serializer for the Blueprint.
 */
class BlueprintSerializer
{
    /**
     * @param string $path the path to the directory where the Blueprint will be saved
     */
    public function __construct(
        private string $path,
    ) {
        if (!is_dir($this->path)) {
            mkdir($this->path, 0777, true);
        }

        if (!is_writable($this->path)) {
            throw new \RuntimeException('Directory is not writable.');
        }

        if (!is_readable($this->path)) {
            throw new \RuntimeException('Directory is not readable.');
        }
    }

    /**
     * Serialize the Blueprint to yaml file.
     *
     * @param Blueprint $blueprint the Blueprint to serialize
     */
    public function serialize(Blueprint $blueprint): void
    {
        $data = [
            $blueprint->blueprintName => $blueprint->normalize(),
        ];
        $yaml = Yaml::dump($data);
        $filePath = $this->path.'/'.$blueprint->blueprintName.'.yaml';

        $res = file_put_contents($filePath, $yaml);
        if (false === $res) {
            throw new \RuntimeException('File not saved.');
        }
    }

    /**
     * Deserialize the Blueprint from yaml file.
     *
     * @param string $blueprintName the name of the Blueprint
     *
     * @return Blueprint the deserialized Blueprint
     */
    public function deserialize(string $blueprintName): Blueprint
    {
        $filePath = $this->path.'/'.$blueprintName.'.yaml';
        $yaml = file_get_contents($filePath);

        if (false === $yaml) {
            throw new \RuntimeException('File not found.');
        }

        $data = Yaml::parse($yaml);
        $blueprint = new Blueprint();
        $blueprint->denormalize($data[$blueprintName]);

        return $blueprint;
    }
}
