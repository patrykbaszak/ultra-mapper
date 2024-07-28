<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Tests\Support\UseCases;

class UseCase
{
    private function __construct(
        /** @var AbstractUseCase[] */
        private array $useCases
    ) {
    }

    public static function create(): self
    {
        $useCases = [
            new DenormalizePersistenceIntoDTO\UseCase(),
            new DenormalizePersistenceIntoEntity\UseCase(),
            new DeserializeRequestBody\UseCase(),
            new MapDTOIntoEntity\UseCase(),
            new SerializeResponseBody\UseCase(),
            new TransformPersistenceIntoArray\UseCase(),
        ];

        return new self($useCases);
    }

    public static function createWith(AbstractUseCase ...$useCases): self
    {
        return new self($useCases);
    }

    /** @return iterable<AbstractUseCase> */
    public function iterate(): iterable
    {
        foreach ($this->useCases as $useCase) {
            yield $useCase;
        }
    }
}
