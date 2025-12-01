<?php

namespace App\Enums\Attributes;

use Attribute;

#[Attribute]
final class Abilities
{
    public function __construct(
        public array $abilities,
    ) {}
}
