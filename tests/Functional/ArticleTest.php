<?php

namespace App\Tests\Functional;

use App\Factory\ArticleFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ArticleTest extends KernelTestCase
{
    use ResetDatabase, Factories, HasBrowser;

    public function testList(): void
    {
        ArticleFactory::new()
            ->sequence([
                ['title' => 'Article 1', 'slug' => 'mercury'],
                ['title' => 'Article 2', 'slug' => 'venus'],
            ])
            ->create()
        ;

        $this->browser()
            ->visit('/')
            ->assertSuccessful()
            ->assertSee('Article 1')
            ->assertSee('Article 2')
        ;
    }

    public function testShow(): void
    {
        ArticleFactory::createOne([
            'title' => 'Article 1',
            'slug' => 'mercury',
        ]);

        $this->browser()
            ->visit('/news/mercury')
            ->assertSuccessful()
            ->assertSee('Article 1')
        ;
    }
}
