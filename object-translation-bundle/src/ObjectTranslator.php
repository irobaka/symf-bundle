<?php

declare(strict_types=1);

namespace Symfon\ObjectTranslationBundle;

use Doctrine\Persistence\ManagerRegistry;
use ReflectionClass;
use Symfon\ObjectTranslationBundle\Mapping\Translatable;
use Symfon\ObjectTranslationBundle\Model\Translation;
use Symfony\Component\Cache\Adapter\NullAdapter;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Contracts\Translation\LocaleAwareInterface;
use WeakMap;

final class ObjectTranslator
{
    private CacheInterface $cache;
    private WeakMap $translatedObjects;

    public function __construct(
        private LocaleAwareInterface $localeAware,
        private string $defaultLocale,
        private string $translationClass,
        private ManagerRegistry $doctrine,
        ?CacheInterface $cache = null,
        private ?int $cacheTtl = null
    ) {
        $this->cache = $cache ?? new NullAdapter();
        $this->translatedObjects = new WeakMap();
    }


    /**
     * @template T of object
     *
     * @param  T  $object
     * @return T
     */
    public function translate(object $object): object
    {
        $locale = $this->localeAware->getLocale();

        if ($this->defaultLocale === $locale) {
            return $object;
        }

        return $this->translatedObjects[$object] ??= new TranslatedObject($object, $this->translationsFor($object, $locale));
    }

    private function translationsFor(object $object, string $locale): array
    {
        $class = new ReflectionClass($object);
        $type = $class->getAttributes(Translatable::class)[0]?->newInstance()->name ?? null;

        if ( ! $type) {
            throw new \LogicException(sprintf('Class "%s" is not translatable.', $object::class));
        }

        $om = $this->doctrine->getManagerForClass($object::class);
        if ( ! $om) {
            throw new \LogicException(sprintf('No object manager found for class "%s"', $object::class));
        }

        $id = $om->getClassMetadata($object::class)->getIdentifierValues($object);

        if (count($id) > 1) {
            throw new \LogicException(sprintf('Class "%s" must have a single identifier to be translatable', $object::class));
        }

        $id = array_first($id);

        return $this->cache->get(
            sprintf('object_translation.%s.%s.%s', $locale, $type, $id),
            function (ItemInterface $item) use ($locale, $type, $id) {
                if ($this->cache instanceof TagAwareCacheInterface) {
                    $item->tag(['object-translation', "object-translation-$type"]);
                }


                if ($this->cacheTtl) {
                    $item->expiresAfter($this->cacheTtl);
                }

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
            },
        );
    }

}
