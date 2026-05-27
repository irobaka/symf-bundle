<?php

declare(strict_types=1);

namespace Symfon\ObjectTranslationBundle\Model;

abstract class Translation {
    public string $objectType;

    public string $objectId;

    public string $locale;

    public string $field;

    public string $value;
}
