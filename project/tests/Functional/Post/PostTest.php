<?php

namespace App\Tests\Functional\Post;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

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
            $this->assertSelectorTextContains('h1', 'Un blog avec Symfony');
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
}