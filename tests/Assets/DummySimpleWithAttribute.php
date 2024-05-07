<?php

namespace PBaszak\UltraMapper\Tests\Assets;

class DummySimpleWithAttribute
{
    public function __construct(
        public string $id,
        public string $name,
        public string $description,
    ) {
    }
}
