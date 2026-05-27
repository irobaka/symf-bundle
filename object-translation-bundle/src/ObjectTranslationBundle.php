<?php

declare(strict_types=1);

namespace Symfon\ObjectTranslationBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Symfon\ObjectTranslationBundle\Model\Translation;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

final class ObjectTranslationBundle extends AbstractBundle
{
    protected string $extensionAlias = 'symfon_object_translation';

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->stringNode('translation_class')
                    ->info("The class name of your translation entity")
                    ->example('App\Entity\Translation')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->validate()
                        ->ifTrue(fn($v) => !is_a($v, Translation::class, true))
                        ->thenInvalid('The translation class %s must extend Symfon\ObjectTranslationBundle\Model\Translation class')
                    ->end()
                ->end()
            ->end();
    }

    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(
            DoctrineOrmMappingsPass::createXmlMappingDriver(
                [__DIR__ . '/../config/doctrine/mapping' => 'Symfon\ObjectTranslationBundle\Model'],
            )
        );
    }

    public function loadExtension(array $config, ContainerConfigurator $configurator, ContainerBuilder $container): void
    {
        $configurator->import('../config/services.php');

        $container->getDefinition('symfon.object_translator')
            ->setArgument(2, $config['translation_class']);
    }
}
