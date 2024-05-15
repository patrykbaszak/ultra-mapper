<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Tests\Blueprint\E2e\Presentation\CLI;

use PBaszak\UltraMapper\Blueprint\Application\Service\BlueprintService;
use PBaszak\UltraMapper\Blueprint\Presentation\CLI\GenerateBlueprint;
use PBaszak\UltraMapper\Tests\Assets\Dummy;
use PBaszak\UltraMapper\Tests\Assets\DummySimple;
use PBaszak\UltraMapper\Tests\Assets\DummySimpleWithAttribute;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Uid\Uuid;

#[Group('e2e')]
class GenerateBlueprintCommandTest extends KernelTestCase
{
    #[Test]
    public function shouldReturnBlueprintOfDummySimple(): void
    {
        $class = DummySimple::class;
        $blueprint = file_get_contents(__DIR__.'/../../../../Assets/pbaszak_ultramapper_tests_assets_dummysimple.yaml');
        $command = new GenerateBlueprint(new BlueprintService(dirname(__DIR__, 5).'/var/ultra-mapper/blueprints/'));
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'class' => $class,
        ]);

        $display = $commandTester->getDisplay();
        $this->assertEquals(rtrim($blueprint, "\n"), rtrim($display, "\n"));
    }

    #[Test]
    public function shouldReturnBlueprintOfDummy(): void
    {
        $class = Dummy::class;
        $blueprint = file_get_contents(__DIR__.'/../../../../Assets/pbaszak_ultramapper_tests_assets_dummy.yaml');
        $command = new GenerateBlueprint(new BlueprintService(dirname(__DIR__, 5).'/var/ultra-mapper/blueprints/'));
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'class' => $class,
        ]);

        $display = $commandTester->getDisplay();
        $this->assertEquals(rtrim($blueprint, "\n"), rtrim($display, "\n"));
    }

    #[Test]
    public function shouldReturnBlueprintOfDummySimpleWithAttribute(): void
    {
        $class = DummySimpleWithAttribute::class;
        $blueprint = file_get_contents(__DIR__.'/../../../../Assets/pbaszak_ultramapper_tests_assets_dummysimplewithattribute.yaml');
        $command = new GenerateBlueprint(new BlueprintService(dirname(__DIR__, 5).'/var/ultra-mapper/blueprints/'));
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'class' => $class,
        ]);

        $display = $commandTester->getDisplay();
        $this->assertEquals(rtrim($blueprint, "\n"), rtrim($display, "\n"));
    }

    #[Test]
    public function shouldSaveBlueprintOfDummySimple(): void
    {
        $dir = '/var/ultra-mapper/blueprints/'.Uuid::v4()->toRfc4122().'/';
        $class = DummySimple::class;
        $blueprintToCompare = file_get_contents(__DIR__.'/../../../../Assets/pbaszak_ultramapper_tests_assets_dummysimple.yaml');
        $command = new GenerateBlueprint(new BlueprintService(dirname(__DIR__, 5).$dir));
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'class' => $class,
            '--save' => true,
        ]);

        $blueprintPath = dirname(__DIR__, 5).$dir.'pbaszak_ultramapper_tests_assets_dummysimple.yaml';

        $this->assertFileExists($blueprintPath);
        $blueprint = file_get_contents($blueprintPath);
        $this->assertEquals(rtrim($blueprint, "\n"), rtrim($blueprintToCompare, "\n"));

        unlink($blueprintPath);
        rmdir(dirname(__DIR__, 5).$dir);
    }
}
