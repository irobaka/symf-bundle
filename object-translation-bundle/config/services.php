<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfon\ObjectTranslationBundle\Command\ObjectTranslationExportCommand;
use Symfon\ObjectTranslationBundle\Command\ObjectTranslationImportCommand;
use Symfon\ObjectTranslationBundle\Command\ObjectTranslationWarmupCommand;
use Symfon\ObjectTranslationBundle\ObjectTranslator;
use Symfon\ObjectTranslationBundle\TranslatableMappingManager;
use Symfon\ObjectTranslationBundle\Twig\ObjectTranslatorExtension;

return static function (ContainerConfigurator $configurator) {
    $configurator
        ->services()
        ->set('symfon.object_translator', ObjectTranslator::class)
        ->args([
            service('translation.locale_switcher'),
            param('kernel.default_locale'),
            service('.symfon.object_translator.mapping_manager')
        ])
        ->tag('twig.runtime')
        ->alias(ObjectTranslator::class, 'symfon.object_translator')


        ->set('.symfon.object_translator.mapping_manager', TranslatableMappingManager::class)
        ->args([
            abstract_arg('Translation class'),
            service('doctrine'),
        ])

        ->set('.symfon.object_translator.warmup_command', ObjectTranslationWarmupCommand::class)
        ->args([
            service('symfon.object_translator'),
            service('.symfon.object_translator.mapping_manager'),
            service('translation.locale_switcher'),
            param('kernel.enabled_locales'),
        ])
        ->tag('console.command')

        ->set('.symfon.object_translator.export_command', ObjectTranslationExportCommand::class)
        ->args([
            service('.symfon.object_translator.mapping_manager'),
        ])
        ->tag('console.command')

        ->set('.symfon.object_translator.import_command', ObjectTranslationImportCommand::class)
        ->args([
            service('.symfon.object_translator.mapping_manager'),
        ])
        ->tag('console.command')

        ->set('.symfon.object_translator.twig_extension', ObjectTranslatorExtension::class)
        ->tag('twig.extension')

    ;
};
