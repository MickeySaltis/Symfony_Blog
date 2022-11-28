<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Post\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        /**
         * Generate 10 Categorys with Faker
         */
        $faker = Factory::create('fr_FR');
        for($i=0; $i < 10; $i++) {
            $category = new Category();
            $category->setName($faker->words(1, true))
                ->setDescriptions(
                    mt_rand(0, 1) === 1 ? $faker->realText(255) : null
                );
            $manager->persist($category);
        }

        $manager->flush();
    }
}