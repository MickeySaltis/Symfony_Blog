<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Post\Category;
use App\Repository\Post\PostRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CategoryFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private PostRepository $postRepository,
    )
    {}

    public function load(ObjectManager $manager): void
    {
        /**
         * Generate 10 Categorys with Faker
         */
        $categories = [];
        $faker = Factory::create('fr_FR');
        for($i=0; $i < 10; $i++) {
            $category = new Category();
            $category->setName($faker->words(1, true) . ' ' . $i)
                ->setDescriptions(
                    mt_rand(0, 1) === 1 ? $faker->realText(255) : null
                );
            $manager->persist($category);
            $categories[] = $category;
        }

        /**
         * Add categories (random 1 to 5) to each post
         */
        $posts = $this->postRepository->findAll();
        foreach($posts as $post) 
        {
            for($i=0; $i < mt_rand(1, 5); $i++)
            {
                $post->addCategory(
                    $categories[mt_rand(0, count($categories) - 1)]
                );
            }
        }

        $manager->flush();
    }

    /**
     * Dependency(DependentFixtureInterface): PostFixtures
     */
    public function getDependencies(): array 
    {
        return [PostFixtures::class];
    }
}