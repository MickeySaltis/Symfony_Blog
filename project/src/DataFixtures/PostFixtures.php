<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Post\Post;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class PostFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        /**
         * Generate 25 Posts with Faker
         */
        $faker = Factory::create('fr_FR');
        for($i=0; $i < 25; $i++) {
            $post = new Post();
            $post->setTitle($faker->words(4, true))
                ->setContent($faker->realText(1800))
                ->setState(mt_rand(0, 2) === 1? Post::STATES[0] : Post::STATES[1]);
            $manager->persist($post);
        }

        $manager->flush();
    }
}
