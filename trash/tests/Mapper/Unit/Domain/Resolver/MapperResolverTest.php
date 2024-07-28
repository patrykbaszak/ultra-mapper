<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Tests\Mapper\Unit\Domain\Resolver;

use PBaszak\UltraMapper\Mapper\Domain\Contract\ClassMapperInterface;
use PBaszak\UltraMapper\Mapper\Domain\Resolver\MapperResolver;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

#[Group('unit')]
class MapperResolverTest extends TestCase
{
    private MapperResolver $resolver;
    private const TEST_DIRECTORY = __DIR__.'/../../../../../var/ultra-mapper/mappers/';

    protected function setUp(): void
    {
        parent::setUp();
        $this->resolver = new MapperResolver(
            self::TEST_DIRECTORY,
            'TestNamespace\\',
        );
    }

    public function testGetMapperShortClassNameThrowsExceptionOnEmptyArguments(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->resolver->getMapperShortClassName();
    }

    public function testGetMapperShortClassNameReturnsConsistentHash(): void
    {
        $args = ['arg1', 'arg2'];
        $hash1 = $this->resolver->getMapperShortClassName(...$args);
        $hash2 = $this->resolver->getMapperShortClassName(...$args);

        $this->assertEquals($hash1, $hash2);
        $this->assertNotEmpty($hash1);
    }

    public function testPerformanceOfGetMapperShortClassName(): void
    {
        $args = ['arg1', 'arg2'];
        $timeStart = microtime(true);
        $this->resolver->getMapperShortClassName(...$args);
        $timeEnd = microtime(true);

        $this->assertLessThan(0.001, $timeEnd - $timeStart);
    }

    public function testResolveReturnsNullForNonExistentMapper(): void
    {
        $shortClassName = $this->resolver->getMapperShortClassName('nonexistent');
        $mapper = $this->resolver->resolve($shortClassName);

        $this->assertNull($mapper);
    }

    public function testSaveAndResolveMapper(): void
    {
        $shortClassName = $this->resolver->getMapperShortClassName('testMapper');
        $mapperFileBody = <<<PHP
<?php
namespace TestNamespace;

use PBaszak\UltraMapper\Mapper\Application\Model\Envelope;
use PBaszak\UltraMapper\Mapper\Domain\Contract\ClassMapperInterface;

class $shortClassName implements ClassMapperInterface
{
    public function map(mixed \$data, mixed &\$output = null): Envelope
    {
        throw new \LogicException('Not implemented.');
    }
}
PHP;
        $this->resolver->save($shortClassName, $mapperFileBody);

        $this->assertFileExists(self::TEST_DIRECTORY.$shortClassName.'.php');

        $mapper = $this->resolver->resolve($shortClassName);

        $this->assertInstanceOf(ClassMapperInterface::class, $mapper);
    }

    public function testSaveMkdir(): void
    {
        $dir = self::TEST_DIRECTORY.Uuid::v4()->toRfc4122().'/';
        $resolver = new MapperResolver(
            $dir,
        );

        $this->assertDirectoryDoesNotExist($dir);
        $resolver->save('test', '');
        $this->assertDirectoryExists($dir);

        unlink($dir.'test.php');
        rmdir($dir);
    }
}
