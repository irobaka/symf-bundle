<?php

declare(strict_types=1);

namespace Symfon\ObjectTranslationBundle\Tests\Integration;

use Symfon\ObjectTranslationBundle\ObjectTranslator;
use Symfon\ObjectTranslationBundle\Tests\Fixtures\Entity\Entity1;
use Symfon\ObjectTranslationBundle\Tests\Fixtures\Entity\Translation;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

use function Zenstruck\Foundry\Persistence\persist;

final class ObjectTranslatorTest extends KernelTestCase
{
    use Factories, ResetDatabase;

    public function testCanAccessService()
    {
        $entity = persist(Entity1::class, [
            'property1' => 'value1',
        ]);

        persist(Translation::class, [
            'objectType' => 'entity1',
            'objectId' => $entity->id,
            'locale' => 'fr',
            'field' => 'property1',
            'value' => 'translated1',
        ]);

        $translator = self::getContainer()->get(ObjectTranslator::class);

        $translated = $translator->translate($entity, 'fr');

        $this->assertSame('translated1', $translated->property1);
    }
}
