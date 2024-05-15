<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Blueprint\Application\Service;

use PBaszak\UltraMapper\Blueprint\Application\Contract\BlueprintInterface;
use PBaszak\UltraMapper\Blueprint\Application\Model\Blueprint;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class BlueprintService implements BlueprintInterface
{
    /** @var array<string, Blueprint> */
    protected array $blueprints = [];

    /**
     * The BlueprintService:
     *  - creates blueprints.
     *  - saves the blueprints in the blueprints directory.
     *
     * @param string $blueprintDir The directory where blueprints are stored. The default value assumes
     *                             that the library is installed via Composer and the blueprints are stored
     *                             in the var/ultra-mapper/blueprints/ directory.
     */
    public function __construct(
        protected string $blueprintDir = __DIR__.'/../../../../../../../var/ultra-mapper/blueprints/',
    ) {
    }

    public function createBlueprint(string $class): Blueprint
    {
        return $this->blueprints[$class] ??= Blueprint::create($class);
    }

    public function saveBlueprint(Blueprint $blueprint): bool
    {
        if (!is_dir($this->blueprintDir)) {
            mkdir($this->blueprintDir, 0777, true);
        }

        $data = $blueprint->normalize();
        $filename = $this->blueprintDir.$blueprint->root.'.yaml';

        return (bool) file_put_contents($filename, Yaml::dump($data, 10));
    }

    public function checkIfBlueprintFilesWasChanged(string $blueprintClass): bool
    {
        $blueprint = $this->createBlueprint($blueprintClass);
        $filename = $this->blueprintDir.$blueprint->root.'.yaml';
        try {
            $originBlueprint = Yaml::parseFile($filename);
        } catch (ParseException) {
            return true;
        }

        return $originBlueprint['fileHashes'] !== $blueprint->filesHashes;
    }
}
