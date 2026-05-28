<?php

declare(strict_types=1);

namespace Symfon\ObjectTranslationBundle;

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
        private TranslatableMappingManager $mappingManager,
        ?CacheInterface $cache = null,
        private ?int $cacheTtl = null,
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
        $type = $this->mappingManager->translatableTypeFor($object);
        $id = $this->mappingManager->idFor($object);

        return $this->cache->get(
            sprintf('object_translation.%s.%s.%s', $locale, $type, $id),
            function (ItemInterface $item) use ($locale, $type, $id) {
                if ($this->cache instanceof TagAwareCacheInterface) {
                    $item->tag(['object-translation', "object-translation-$type"]);
                }

                if ($this->cacheTtl) {
                    $item->expiresAfter($this->cacheTtl);
                }

                return $this->mappingManager->translationsFor($locale, $type, $id);
            },
        );
    }

}
