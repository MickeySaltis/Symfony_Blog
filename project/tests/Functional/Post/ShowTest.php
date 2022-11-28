<?php

namespace App\Tests\Functional\Post;

use App\Entity\Post\Post;
use App\Repository\Post\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ShowTest extends WebTestCase
{
    /**
     * Testing a simple static page (Detail of a post)
     */
        public function testShowPostPageWorks(): void
        {
            $client = static::createClient();

            /** @var UrlGeneratorInterface */
            $urlGeneratorInterface = $client->getContainer()->get('router');

            /** @var EntityManagerInterface */
            $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

            /** @var PostRepository */
            $postRepository = $entityManager->getRepository(Post::class);

            /** @var Post */
            $post = $postRepository->findOneBy([]);

            /**
             * Test Url
             */
            $client->request(
                Request::METHOD_GET,
                $urlGeneratorInterface->generate('post_show', ['slug' => $post->getSlug()])
            );

            /**
             * Test response
             */
            $this->assertResponseIsSuccessful();
            $this->assertResponseStatusCodeSame(Response::HTTP_OK);

            /**
             * Test title
             */
            $this->assertSelectorExists('h1');
            $this->assertSelectorTextContains('h1', ucfirst($post->getTitle()));
        }

    /**
     * Test of the button to return to the Home page
     */
        public function testReturnToBlogWorks()
        {
            $client = static::createClient();

            /** @var UrlGeneratorInterface */
            $urlGeneratorInterface = $client->getContainer()->get('router');

            /** @var EntityManagerInterface */
            $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

            /** @var PostRepository */
            $postRepository = $entityManager->getRepository(Post::class);

            /** @var Post */
            $post = $postRepository->findOneBy([]);

            /**
             * Test Url
             */
            $crawler = $client->request(
                Request::METHOD_GET,
                $urlGeneratorInterface->generate('post_show', ['slug' => $post->getSlug()])
            );

            /**
             * Test response
             */
            $this->assertResponseIsSuccessful();
            $this->assertResponseStatusCodeSame(Response::HTTP_OK);

            /**
             * Test back button
             */
            $link = $crawler->selectLink('Retourner au blog')->link()->getUri();
            $crawler = $client->request(Request::METHOD_GET, $link);

            /**
             * Test response + Test back to the home page 
             */
            $this->assertResponseIsSuccessful();
            $this->assertResponseStatusCodeSame(Response::HTTP_OK);
            $this->assertRouteSame('post_index');
        }

    /**
     * Test on the share button for Facebook
     */
        public function testShareOnFacebookWorks(): void
        {
            $client = static::createClient();

            /** @var UrlGeneratorInterface */
            $urlGeneratorInterface = $client->getContainer()->get('router');

            /** @var EntityManagerInterface */
            $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

            /** @var PostRepository */
            $postRepository = $entityManager->getRepository(Post::class);

            /** @var Post */
            $post = $postRepository->findOneBy([]);

            $postLink = $urlGeneratorInterface->generate('post_show', ['slug' => $post->getSlug()]);

            /**
             * Test Url
             */
            $crawler = $client->request(
                Request::METHOD_GET,
                $postLink
            );

            /**
             * Test response
             */
            $this->assertResponseIsSuccessful();
            $this->assertResponseStatusCodeSame(Response::HTTP_OK);

            /**
             * Test Facebook button
             */
            $link = $crawler->filter('.share_facebook')->link()->getUri();

            $this->assertStringContainsString(
                "https://www.facebook.com/sharer/sharer.php",
                $link
            );
            $this->assertStringContainsString(
                $postLink,
                $link
            );
        }

    /**
     * Test on the share button for Twitter
     */
        public function testShareOnTwitterWorks(): void
        {
            $client = static::createClient();

            /** @var UrlGeneratorInterface */
            $urlGeneratorInterface = $client->getContainer()->get('router');

            /** @var EntityManagerInterface */
            $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

            /** @var PostRepository */
            $postRepository = $entityManager->getRepository(Post::class);

            /** @var Post */
            $post = $postRepository->findOneBy([]);

            $postLink = $urlGeneratorInterface->generate('post_show', ['slug' => $post->getSlug()]);

            /**
             * Test Url
             */
            $crawler = $client->request(
                Request::METHOD_GET,
                $postLink
            );

            /**
             * Test response
             */
            $this->assertResponseIsSuccessful();
            $this->assertResponseStatusCodeSame(Response::HTTP_OK);

            /**
             * Test Twitter button
             */
            $link = $crawler->filter('.share_twitter')->link()->getUri();

            $this->assertStringContainsString(
                "https://twitter.com/intent/tweet",
                $link
            );
            $this->assertStringContainsString(
                $postLink,
                $link
            );
        }
}