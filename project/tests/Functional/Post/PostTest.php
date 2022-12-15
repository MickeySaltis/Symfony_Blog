<?php

namespace App\Tests\Functional\Post;

use App\Entity\Post\Tag;
use App\Entity\Post\Post;
use App\Repository\Post\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PostTest extends WebTestCase
{
    /**
     * Testing a simple static page (Home)
     */
        public function testBlogPageWorks(): void 
        {
            /**
             * Test Url
             */
            $client = static::createClient();
            $client->request(Request::METHOD_GET, '/');

            /**
             * Test response
             */
            $this->assertResponseIsSuccessful();
            $this->assertResponseStatusCodeSame(Response::HTTP_OK);

            /**
             * Test title
             */
            $this->assertSelectorExists('h1');
            $this->assertSelectorTextContains('h1', 'Symfony Blog');
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
            $crawler = $client->request(Request::METHOD_GET, '/');

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
            $crawler = $client->request(Request::METHOD_GET, '/');

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

        public function testSearchBarWorks(): void
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

            /** @var Tag */
            $tag = $post->getTags()[0];

            /**
             * Test Url
             */
            $crawler = $client->request(Request::METHOD_GET, $urlGeneratorInterface->generate('post_index'));

            /**
             * Test response
             */
            $this->assertResponseIsSuccessful();
            $this->assertResponseStatusCodeSame(Response::HTTP_OK);

            /**
             * Searchs
             */
            $searchs = [
                substr($post->getTitle(), 0, 3),
                substr($tag->getName(), 0, 3)
            ];

            /**
             * Search in the search bar
             */
            foreach($searchs as $search) 
            {
                /* Search Form */
                $form = $crawler->filter('form[name=search]')->form(['search[q]' => $search]);

                /* Submit */
                $crawler = $client->submit($form);

                /**
                 * Response
                 */
                $this->assertResponseIsSuccessful();
                $this->assertResponseStatusCodeSame(Response::HTTP_OK);
                $this->assertRouteSame('post_index');

                /**
                 *  Title Post / Number Posts / Count
                 */
                $postsTitle = $crawler->filter('div.card h5');
                $nbPosts = count($crawler->filter('div.card'));
                $count = 0;

                /**
                 * Verification of the concordance of the results with the research
                 */
                foreach($postsTitle as $title)
                {
                    if(str_contains($title->textContent, $search) || str_contains($tag->getName(), $search))
                    {
                        $count++;
                    }
                }
                $this->assertEquals($nbPosts, $count);
            }
        }

        public function testSearchBarReturnsNoItems(): void
        {
            $client = static::createClient();

            /** @var UrlGeneratorInterface */
            $urlGeneratorInterface = $client->getContainer()->get('router');

            /**
             * Test Url
             */
            $crawler = $client->request(Request::METHOD_GET, $urlGeneratorInterface->generate('post_index'));

            /**
             * Test response
             */
            $this->assertResponseIsSuccessful();
            $this->assertResponseStatusCodeSame(Response::HTTP_OK);

            /* Search Form */
            $form = $crawler->filter('form[name=search]')->form(['search[q]' => 'aazzeerrttyy']);

            /* Submit */
            $crawler = $client->submit($form);

            /**
             * Response
             */
            $this->assertResponseIsSuccessful();
            $this->assertResponseStatusCodeSame(Response::HTTP_OK);
            $this->assertRouteSame('post_index');

            /**
             * Verification that there is no result
             */
            $this->assertSelectorNotExists('div.card');
        }
}