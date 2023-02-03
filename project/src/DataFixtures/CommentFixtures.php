<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Post\Comment;
use App\Repository\UserRepository;
use App\Repository\Post\PostRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private PostRepository $postRepository,
        private UserRepository $userRepository,
    ) {}

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $users = $this->userRepository->findAll();
        $posts = $this->postRepository->findAll();

        /**
         * Each Post receives between 0 and 5 comments from randomly selected Users
         */
        foreach ($posts as $post) 
        {
            for ($i = 0; $i < mt_rand(0, 5); $i++)
            {
                $comment = new Comment();
                $comment->setContent($faker->realText(100))
                        ->setIsApproved(mt_rand(0, 3) === 0? false : true)
                        ->setAuthor($users[mt_rand(0, count($users) - 1)])
                        ->setPost($post);
                
                $manager->persist($comment);
                $post->addComment($comment);
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