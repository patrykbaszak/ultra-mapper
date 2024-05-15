<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Build\Application\Placeholder;

class Callback
{
    public const INITIALIZATION = '{{callback.initialization}}';
    public const CALLBACK_IF_NOT_EXISTS = '{{callback.if_not_exists}}';
    public const INITIALIZATION_IF_NOT_EXISTS = '{{callback.initialization_if_not_exists}}';
    public const CALLBACK = '{{callback.on_variable}}';
    public const ASSIGNMENT = '{{callback.wrapped_by_setter}}';
    public const CALLBACK_ON_FAILURE = '{{callback.on_failure}}';
    public const FINAL_CALLBACK = '{{callback.final}}';
}
