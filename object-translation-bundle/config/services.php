<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfon\ObjectTranslationBundle\ObjectTranslator;
use Symfon\ObjectTranslationBundle\Twig\ObjectTranslatorExtension;

return static function (ContainerConfigurator $configurator) {
    $configurator
        ->services()
        ->set('symfon.object_translator', ObjectTranslator::class)
        ->args([
            service('translation.locale_switcher'),
            param('kernel.default_locale'),
            abstract_arg('Translation class'),
            service('doctrine'),
        ])
        ->tag('twig.runtime')
        ->alias(ObjectTranslator::class, 'symfon.object_translator')


        ->set('.symfon.object_translator.twig_extension', ObjectTranslatorExtension::class)
        ->tag('twig.extension')

    ;
};
