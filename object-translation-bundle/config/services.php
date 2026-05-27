<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfon\ObjectTranslationBundle\ObjectTranslator;

return static function (ContainerConfigurator $configurator){
    $configurator->services()
        ->set('symfon.object_translator', ObjectTranslator::class)
        ->args([
            service('translation.locale_switcher'),
            param('kernel.default_locale'),
            abstract_arg('Translation class'),
            service('doctrine'),
        ])
        ->alias(ObjectTranslator::class, 'symfon.object_translator');
};
