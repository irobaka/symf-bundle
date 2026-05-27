<?php

declare(strict_types=1);

namespace Symfon\ObjectTranslationBundle\Twig;

use Symfon\ObjectTranslationBundle\ObjectTranslator;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * @internal
 */
final class ObjectTranslatorExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('translate_object', [ObjectTranslator::class, 'translate']),
        ];
    }
}
