<?php

declare(strict_types=1);

namespace Symfon\ObjectTranslationBundle\Tests\Fixtures\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfon\ObjectTranslationBundle\Model\Translation as BaseTranslation;

#[ORM\Entity]
final class Translation extends BaseTranslation {

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;
}
