<?php

declare(strict_types=1);

namespace Symfon\ObjectTranslationBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Symfon\ObjectTranslationBundle\Model\Translation;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

final class ObjectTranslationBundle extends AbstractBundle
{
    protected string $extensionAlias = 'symfon_object_translation';

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition
            ->rootNode()
                ->children()
                    ->stringNode('translation_class')
                        ->info("The class name of your translation entity")
                        ->example('App\Entity\Translation')
                        ->isRequired()
                        ->cannotBeEmpty()
                        ->validate()
                            ->ifTrue(fn($v) => ! is_a($v, Translation::class, true))
                            ->thenInvalid('The translation class %s must extend Symfon\ObjectTranslationBundle\Model\Translation class')
                        ->end()
                    ->end()
                    ->arrayNode('cache')
                        ->info("Cache settings for object translations")
                        ->canBeDisabled()
                        ->children()
                            ->stringNode('pool')
                                ->info('The cache pool to use for storing object translations')
                                ->defaultValue('cache.app')
                            ->end()
                            ->integerNode('ttl')
                                ->info('The number of seconds to store object translations, null for no expiration')
                                ->defaultValue(null)
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(
            DoctrineOrmMappingsPass::createXmlMappingDriver(
                [__DIR__ . '/../config/doctrine/mapping' => 'Symfon\ObjectTranslationBundle\Model'],
            ),
        );
    }

    public function loadExtension(array $config, ContainerConfigurator $configurator, ContainerBuilder $container): void
    {
        $configurator->import('../config/services.php');

        $objectTranslatorDefinition = $container->getDefinition('symfon.object_translator');

        if ($config['cache']['enabled']) {
            $objectTranslatorDefinition->setArgument(3, new Reference($config['cache']['pool']));
            $objectTranslatorDefinition->setArgument(4, $config['cache']['ttl']);
        }

        $mappingManagerDefinition = $container->getDefinition('.symfon.object_translator.mapping_manager');

        $mappingManagerDefinition->setArgument(0, $config['translation_class']);
    }
}
