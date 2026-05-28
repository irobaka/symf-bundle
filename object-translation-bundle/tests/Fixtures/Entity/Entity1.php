<?php

namespace Symfon\ObjectTranslationBundle\Tests\Fixtures\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfon\ObjectTranslationBundle\Mapping\Translatable;
use Symfon\ObjectTranslationBundle\Mapping\TranslatableProperty;

#[Translatable('entity1')]
#[ORM\Entity]
class Entity1
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ORM\Column]
    #[TranslatableProperty]
    public string $property1;
}
