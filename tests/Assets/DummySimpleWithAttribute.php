<?php

namespace PBaszak\UltraMapper\Tests\Assets;

use PBaszak\UltraMapper\Attribute\Callback;
use PBaszak\UltraMapper\Attribute\Ignore;

class DummySimpleWithAttribute
{
    public function __construct(
        #[Callback('test')]
        #[Callback('test2')]
        public string $id,
        public string $name,
        #[Ignore]
        public string $description,
    ) {
    }
}
