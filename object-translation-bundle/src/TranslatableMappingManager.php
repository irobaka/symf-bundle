<?php

declare(strict_types=1);

namespace Symfon\ObjectTranslationBundle;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\Proxy;
use ReflectionClass;
use Symfon\ObjectTranslationBundle\Mapping\Translatable;
use Symfon\ObjectTranslationBundle\Model\Translation;

/**
 * @internal
 */
final class TranslatableMappingManager
{
    public function __construct(
        private string $translationClass,
        private ManagerRegistry $doctrine,
    ) {}

    public function translatableTypeFor(object $object): string
    {
        $class = new ReflectionClass($object);

        if ($class->implementsInterface(Proxy::class)) {
            $class = $class->getParentClass();
        }

        $type = ($class->getAttributes(Translatable::class)[0] ?? null)?->newInstance()->name ?? null;

        if ( ! $type) {
            throw new \LogicException(sprintf('Class "%s" is not translatable.', $object::class));
        }

        return $type;
    }

    public function idFor(object $object): string
    {
        $om = $this->doctrine->getManagerForClass($object::class);
        if ( ! $om) {
            throw new \LogicException(sprintf('No object manager found for class "%s"', $object::class));
        }

        $id = $om->getClassMetadata($object::class)->getIdentifierValues($object);

        if (count($id) > 1) {
            throw new \LogicException(sprintf('Class "%s" must have a single identifier to be translatable', $object::class));
        }

        return (string)array_first($id);
    }

    public function translationsFor(string $locale, string $type, string $id): array
    {
        /** @var Translation[] $translations */
        $translations = $this->doctrine->getRepository($this->translationClass)->findBy([
            'locale' => $locale,
            'objectType' => $type,
            'objectId' => $id,
        ]);

        $translationValues = [];

        foreach ($translations as $translation) {
            $translationValues[$translation->field] = $translation->value;
        }

        return $translationValues;
    }

    public function allTranslatableObjects(): iterable
    {
        foreach ($this->doctrine->getManagers() as $om) {
            foreach ($om->getMetadataFactory()->getAllMetadata() as $metadata) {
                $class = $metadata->getName();

                if ( ! new ReflectionClass($class)->getAttributes(Translatable::class)) {
                    continue;
                }

                yield from $this->doctrine->getRepository($class)->findAll();
            }
        }
    }
}
