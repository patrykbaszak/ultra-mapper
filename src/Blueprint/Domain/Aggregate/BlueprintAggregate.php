<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Blueprint\Domain\Aggregate;

use PBaszak\UltraMapper\Blueprint\Domain\Entity\Blueprint;
use PBaszak\UltraMapper\Blueprint\Domain\Exception\BlueprintException;
use PBaszak\UltraMapper\Blueprint\Domain\Normalizer\Normalizable;

class BlueprintAggregate implements Normalizable
{
    public function __construct(
        public string $root,
        /** @var array<string, Blueprint> */
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
    public static function create(string|Blueprint $rootClass): self
    {
        $blueprint = $rootClass instanceof Blueprint ? $rootClass : Blueprint::create($rootClass, null);
        $rootClass = $blueprint->name;

        $instance = new self(
            $blueprint->blueprintName,
            [$blueprint->blueprintName => $blueprint],
            $blueprint->hasDeclarationFile() ? [$blueprint->filePath => $blueprint->fileHash] : [],
            [
                'Blueprint Aggregate created. Root class: '.$rootClass.'.',
            ]
        );

        $blueprint->aggregate = $instance;

        return $instance;
    }

    public function addBlueprint(Blueprint $blueprint): void
    {
        if (array_key_exists($blueprint->blueprintName, $this->blueprints)) {
            $this->events[] = 'Blueprint '.$blueprint->name.' already exists. Blueprint name: '.$blueprint->blueprintName.'.';

            return;
        }

        $this->blueprints[$blueprint->blueprintName] = $blueprint;
        $this->events[] = 'Blueprint '.$blueprint->name.' added. Blueprint name: '.$blueprint->blueprintName.'.';

        if ($blueprint->hasDeclarationFile()) {
            if (array_key_exists($blueprint->filePath, $this->filesHashes) && $this->filesHashes[$blueprint->filePath] !== $blueprint->fileHash) {
                throw new BlueprintException('File hash mismatch. File: '.$blueprint->filePath.'.', 5921);
            }

            $this->addFileHash($blueprint->filePath, $blueprint->fileHash);
        }
    }

    public function addFileHash(string $filePath, string $fileHash): void
    {
        $this->filesHashes[$filePath] = $fileHash;
        $this->events[] = 'File hash added. File: '.$filePath.', hash: '.$fileHash.'.';
    }

    public function addEvent(string $event): void
    {
        $this->events[] = $event;
    }

    public function normalize(): array
    {
        return [
            'root' => $this->root,
            'blueprints' => array_map(fn (Normalizable&Blueprint $blueprint) => $blueprint->normalize(), $this->blueprints),
            'filesHashes' => $this->filesHashes,
            'events' => $this->events,
        ];
    }
}
