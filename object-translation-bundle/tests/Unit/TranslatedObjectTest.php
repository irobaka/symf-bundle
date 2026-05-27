<?php

declare(strict_types=1);

namespace Symfon\ObjectTranslationBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfon\ObjectTranslationBundle\TranslatedObject;

final class TranslatedObjectTest extends TestCase
{
    public function testCanAccessUnderlyingObject(): void
    {
        /** @var TranslatedObject<ObjectForTranslationStub> $object */
        $object = new TranslatedObject(new ObjectForTranslationStub(), []);

        $this->assertSame('value1', $object->prop1);
        $this->assertSame('value2', $object->prop2());
        $this->assertSame('value3', $object->getProp3());
    }

    public function testCallUsesGetterIfAvailable(): void
    {
        $object = new TranslatedObject(new ObjectForTranslationStub(), []);

        $this->assertSame('value3', $object->prop3());
    }

    public function testCanTranslateProperties(): void
    {
        $object = new TranslatedObject(new ObjectForTranslationStub(), [
            'prop1' => 'translated1',
            'prop2' => 'translated2',
            'prop3' => 'translated3',
        ]);

        $this->assertSame('translated1', $object->prop1);
        $this->assertSame('translated2', $object->prop2());
        $this->assertSame('translated3', $object->getProp3());
    }
}

class ObjectForTranslationStub
{
    public string $prop1 = 'value1';
    private string $prop2 = 'value2';
    private string $prop3 = 'value3';

    public function prop2(): string
    {
        return $this->prop2;
    }

    public function getProp3(): string
    {
        return $this->prop3;
    }
}
