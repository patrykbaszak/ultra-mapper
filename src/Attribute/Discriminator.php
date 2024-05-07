<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Attribute;

/**
 * Discriminator on the Interface or Abstract class level will
 * be used to determine the concrete class to instantiate.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_CLASS)]
class Discriminator
{
}
