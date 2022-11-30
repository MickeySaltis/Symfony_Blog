<?php

namespace App\Tests\Unit;

use App\Entity\Post\Post;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PostTest extends KernelTestCase
{

    /**
     * Entity
     */
    public function getEntity(): Post
    {
        return (new Post())
            ->setTitle('Post #1')
            ->setSlug('Post #1')
            ->setContent('Content #1')
            ->setUpdatedAt(new \DatetimeImmutable())
            ->setCreatedAt(new \DatetimeImmutable());
    }

    /**
     * Test if the entity is valid
     */
        public function testPostEntityIsValid(): void
        {
            self::bootKernel();
            $container = static::getContainer();

            $post = $this->getEntity();

            /**
             *The errors
            */
            $errors = $container->get('validator')->validate($post);

            /**
             * Count the errors / If there are no errors it's ok
             */
            $this->assertCount(0, $errors);

        }

    /**
     * Test if the empty field in the title returns an error
     */
        public function testInvalidName()
        {
            self::bootKernel();
            $container = static::getContainer();

            $post = $this->getEntity();
            $post->setTitle('');

            /**
             *The errors
            */
            $errors = $container->get('validator')->validate($post);

            /**
             * Count the errors / If there are no errors it's ok
             */
            $this->assertCount(1, $errors);
        }
}