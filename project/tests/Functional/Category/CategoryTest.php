<?php

namespace App\Tests\Functional\Category;

use App\Entity\Post\Category;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\Post\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CategoryTest extends WebTestCase
{
    /**
     * Testing a simple static page (Category)
     */
        public function testCategoryPageWorks(): void 
        {
            /**
             * Test Url
             */
            $client = static::createClient();

            /** @var UrlGeneratorInterface */
            $urlGeneratorInterface = $client->getContainer()->get('router');

            /** @var EntityManagerInterface */
            $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

            /** @var CategoryRepository */
            $categoryRepository = $entityManager->getRepository(Category::class);

            /** @var Category */
            $category = $categoryRepository->findOneBy([]);

            $client->request(
                Request::METHOD_GET,
                $urlGeneratorInterface->generate('category_index', ['slug' => $category->getSlug()])
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
            $this->assertSelectorTextContains('h1', 'Catégorie: '.ucfirst($category->getName()));
        }

    /**
     * Test a pagination
     */
        public function testPaginationWorks(): void 
        {
            /**
             * Test Url
             */
            $client = static::createClient();

            /** @var UrlGeneratorInterface */
            $urlGeneratorInterface = $client->getContainer()->get('router');

            /** @var EntityManagerInterface */
            $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

            /** @var CategoryRepository */
            $categoryRepository = $entityManager->getRepository(Category::class);

            /** @var Category */
            $category = $categoryRepository->findOneBy([]);

            $crawler =$client->request(
                Request::METHOD_GET,
                $urlGeneratorInterface->generate('category_index', ['slug' => $category->getSlug()])
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

    /**
     * Test the links of the dropdown category 
     */
        public function testDropdownWorks(): void
        {
            /**
             * Test Url
             */
            $client = static::createClient();

            /** @var UrlGeneratorInterface */
            $urlGeneratorInterface = $client->getContainer()->get('router');

            /** @var EntityManagerInterface */
            $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

            /** @var CategoryRepository */
            $categoryRepository = $entityManager->getRepository(Category::class);

            /** @var Category */
            $category = $categoryRepository->findOneBy([]);

            $crawler =$client->request(
                Request::METHOD_GET,
                $urlGeneratorInterface->generate('category_index', ['slug' => $category->getSlug()])
            );

            /**
             * Test response
             */
            $this->assertResponseIsSuccessful();
            $this->assertResponseStatusCodeSame(Response::HTTP_OK);

            /**
             * Test link category dropdown
             */
            $link = $crawler->filter('.dropdown-menu > li > a')->link()->getUri();
            $client->request(Request::METHOD_GET, $link);

            /**
             * Test response
             */
            $this->assertResponseIsSuccessful();
            $this->assertResponseStatusCodeSame(Response::HTTP_OK);
            $this->assertRouteSame('category_index');
        }
}