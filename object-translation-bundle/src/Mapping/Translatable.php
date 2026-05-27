<?php

declare(strict_types=1);

namespace Symfon\ObjectTranslationBundle\Mapping;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class Translatable {
    public function __construct(
        public string $name,
    ) {}
}
