<?php

namespace App\Enums\Attributes;

use Attribute;

#[Attribute]
final class Description
{
    public function __construct(
        public string $description,
    ) {}
}
