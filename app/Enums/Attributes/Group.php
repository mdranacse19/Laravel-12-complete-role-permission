<?php

namespace App\Enums\Attributes;

use Attribute;

#[Attribute]
final class Group
{
    public function __construct(
        public string $group,
    ) {}
}
