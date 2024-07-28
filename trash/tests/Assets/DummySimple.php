<?php

namespace PBaszak\UltraMapper\Tests\Assets;

class DummySimple
{
    public function __construct(
        public string $id,
        public string $name,
        public string $description,
    ) {
    }
}
