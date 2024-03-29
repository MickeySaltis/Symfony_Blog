<?php

namespace App\DataFixtures;


use App\Repository\UserRepository;
use App\Repository\Post\PostRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class LikeFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private PostRepository $postRepository,
        private UserRepository $userRepository,
    ) {}

    public function load(ObjectManager $manager): void
    {
        $users = $this->userRepository->findAll();
        $posts = $this->postRepository->findAll();

        /**
         * Each Post receives between 0 and 5 likes from randomly selected Users
         */
        foreach ($posts as $post) 
        {
            for ($i = 0; $i < mt_rand(0, 5); $i++)
            {
                $post->addLike($users[mt_rand(0, count($users) - 1)]);
            }
        }
        $manager->flush();
        
    }

    /**
     * Dependency(DependentFixtureInterface): UserFixtures / PostFixtures
     */
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            PostFixtures::class,
        ];
    }
}