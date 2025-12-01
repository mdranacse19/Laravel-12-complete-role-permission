<?php

namespace App\Enums\Attributes;

use Attribute;

#[Attribute]
final class Module
{
    public function __construct(
        public string $module,
    ) {}
}
