<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Tests\Blueprint\E2e;

use PBaszak\UltraMapper\Blueprint\Presentation\CLI\GenerateBlueprint;
use PBaszak\UltraMapper\Tests\Assets\Dummy;
use PBaszak\UltraMapper\Tests\Assets\DummySimple;
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
        $blueprint = file_get_contents(__DIR__.'/../../Assets/pbaszak_ultramapper_tests_assets_dummysimple.yaml');
        $command = new GenerateBlueprint(dirname(__DIR__, 3));
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
        $blueprint = file_get_contents(__DIR__.'/../../Assets/pbaszak_ultramapper_tests_assets_dummy.yaml');
        $command = new GenerateBlueprint(dirname(__DIR__, 3));
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
        $blueprintToCompare = file_get_contents(__DIR__.'/../../Assets/pbaszak_ultramapper_tests_assets_dummysimple.yaml');
        $command = new GenerateBlueprint(dirname(__DIR__, 3), $dir);
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'class' => $class,
            '--save' => true,
        ]);

        $blueprintPath = dirname(__DIR__, 3).$dir.'pbaszak_ultramapper_tests_assets_dummysimple.yaml';

        $this->assertFileExists($blueprintPath);
        $blueprint = file_get_contents($blueprintPath);
        $this->assertEquals(rtrim($blueprint, "\n"), rtrim($blueprintToCompare, "\n"));

        unlink($blueprintPath);
        rmdir(dirname(__DIR__, 3).$dir);
    }
}
