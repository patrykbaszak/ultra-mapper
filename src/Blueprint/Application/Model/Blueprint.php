<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Blueprint\Application\Model;

use PBaszak\UltraMapper\Blueprint\Application\Model\Assets\AttributeBlueprint;
use PBaszak\UltraMapper\Blueprint\Application\Model\Assets\ClassBlueprint;
use PBaszak\UltraMapper\Blueprint\Application\Model\Assets\MethodBlueprint;
use PBaszak\UltraMapper\Blueprint\Application\Model\Assets\ParameterBlueprint;
use PBaszak\UltraMapper\Blueprint\Application\Model\Assets\PropertyBlueprint;
use PBaszak\UltraMapper\Shared\Infrastructure\Normalization\Normalizable;

class Blueprint implements Normalizable
{
    /** @var array<string, mixed> */
    public array $options = [];

    public function __construct(
        public string $root,
        /** @var array<string, ClassBlueprint> */
        public array $blueprints,
        /** @var array<string, string> */
        public array $filesHashes,
        /** @var array<int, string> */
        public array $events
    ) {
    }

    /**
     * @param class-string $rootClass
     */
    public static function create(string|ClassBlueprint $rootClass): self
    {
        $blueprint = $rootClass instanceof ClassBlueprint ? $rootClass : ClassBlueprint::create($rootClass, null);
        $rootClass = $blueprint->name;

        $instance = $blueprint->blueprint ?? new self(
            $blueprint->blueprintName,
            [],
            [],
            []
        );

        if (!in_array('Blueprint created. Root class: '.$rootClass.'.', $instance->events, true)) {
            $instance->addEvent('Blueprint created. Root class: '.$rootClass.'.');
        }

        if (!array_key_exists($blueprint->blueprintName, $instance->blueprints)) {
            $instance->addBlueprint($blueprint);
        }

        if ($blueprint->hasDeclarationFile() && !array_key_exists($blueprint->filePath, $instance->filesHashes)) {
            /* @phpstan-ignore-next-line types of $instance->filePath and $instance->fileHash are for sure strings */
            $instance->addFileHash($blueprint->filePath, $blueprint->fileHash);
        }

        $blueprint->blueprint = $instance;

        return $instance;
    }

    public static function getBlueprint(
        AttributeBlueprint|ClassBlueprint|MethodBlueprint|ParameterBlueprint|PropertyBlueprint $asset
    ): ?self {
        return match (get_class($asset)) {
            ClassBlueprint::class => $asset->blueprint,
            default => self::getBlueprint($asset->parent)
        };
    }

    public static function getClassBlueprint(
        AttributeBlueprint|ClassBlueprint|MethodBlueprint|ParameterBlueprint|PropertyBlueprint $asset
    ): ClassBlueprint {
        return match (get_class($asset)) {
            ClassBlueprint::class => $asset,
            default => self::getClassBlueprint($asset->parent)
        };
    }

    public function addBlueprint(ClassBlueprint $blueprint): void
    {
        if (array_key_exists($blueprint->blueprintName, $this->blueprints)) {
            $this->addEvent('Class Blueprint '.$blueprint->name.' already exists. Blueprint name: '.$blueprint->blueprintName.'.');

            return;
        }

        $this->blueprints[$blueprint->blueprintName] = $blueprint;
        $this->addEvent('Class Blueprint '.$blueprint->name.' added. Blueprint name: '.$blueprint->blueprintName.'.');

        if ($blueprint->hasDeclarationFile()) {
            /* @phpstan-ignore-next-line */
            $this->addFileHash($blueprint->filePath, $blueprint->fileHash);
        }
    }

    public function addFileHash(string $filePath, string $fileHash): void
    {
        if (array_key_exists($filePath, $this->filesHashes)) {
            $this->addEvent('File hash already exists. File: '.$filePath.', hash: '.$fileHash.'.');

            return;
        }
        $this->filesHashes[$filePath] = $fileHash;
        $this->addEvent('File hash added. File: '.$filePath.', hash: '.$fileHash.'.');
    }

    public function addEvent(string $event): void
    {
        $this->events[] = $event;
    }

    public function normalize(): array
    {
        return [
            'root' => $this->root,
            'blueprints' => array_map(fn (Normalizable&ClassBlueprint $blueprint) => $blueprint->normalize(), $this->blueprints),
            'filesHashes' => $this->filesHashes,
            'events' => $this->events,
        ];
    }

    public function __clone(): void
    {
        $this->blueprints = array_map(fn (ClassBlueprint $blueprint) => clone $blueprint, $this->blueprints);
        foreach ($this->blueprints as $blueprint) {
            $blueprint->blueprint = $this;
        }
        $this->addEvent('Blueprint cloned.');
    }
}
