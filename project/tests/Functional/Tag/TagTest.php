<?php

namespace App\Tests\Functional\Tag;

use App\Entity\Post\Tag;
use App\Repository\Post\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TagTest extends WebTestCase
{
    /**
     * Testing a simple static page (Tag)
     */
        public function testPageWorks(): void
        {
            $client = static::createClient();

            /** @var UrlGeneratorInterface */
            $urlGeneratorInterface = $client->getContainer()->get('router');

            /** @var EntityManagerInterface */
            $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

            /** @var TagRepository */
            $tagRepository = $entityManager->getRepository(Tag::class);

            /** @var Tag */
            $tag = $tagRepository->findOneBy([]);

            /**
             * Test Url
             */
            $client->request(
                Request::METHOD_GET,
                $urlGeneratorInterface->generate('tag_index', ['slug' => $tag->getSlug()])
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
            $this->assertSelectorTextContains('h1', 'Tag: ' . ucfirst($tag->getName()));

        }

    /**
     * Test a pagination
     */
        public function testPaginationWorks(): void 
        {
            $client = static::createClient();

            /** @var UrlGeneratorInterface */
            $urlGeneratorInterface = $client->getContainer()->get('router');

            /** @var EntityManagerInterface */
            $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

            /** @var TagRepository */
            $tagRepository = $entityManager->getRepository(Tag::class);

            /** @var Tag */
            $tag = $tagRepository->findOneBy([]);

            /**
             * Test Url
             */
            $crawler = $client->request(
                Request::METHOD_GET,
                $urlGeneratorInterface->generate('tag_index', ['slug' => $tag->getSlug()])
            );

            /**
             * Test response
             */
            $this->assertResponseIsSuccessful();
            $this->assertResponseStatusCodeSame(Response::HTTP_OK);

            /**
             * Test on the number of posts displayed on a page (=9)
             */
            $posts = $crawler->filter('div.card');
            $this->assertEquals(9, count($posts));

            /**
             * Test the pagination link (page 2)
             */
            $link = $crawler->selectLink('2')->extract(['href'])[0];
            $crawler = $client->request(Request::METHOD_GET, $link);

            /**
             * Test response
             */
            $this->assertResponseIsSuccessful();
            $this->assertResponseStatusCodeSame(Response::HTTP_OK);

            /**
             * Test if there is at least one post on the second page
             */
            $posts = $crawler->filter('div.card');
            $this->assertGreaterThanOrEqual(1, count($posts));
        }
}