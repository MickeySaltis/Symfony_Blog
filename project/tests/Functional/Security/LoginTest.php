<?php

namespace App\Tests\Functional\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

    class LoginTest extends WebTestCase
    {
        /**
         * Test for Login
         */
        public function testLoginWorks(): void
        {
            $client = static::createClient();

            /** @var UrlGeneratorInterface */
            $urlGenerator = $client->getContainer()->get('router');

            /**
             * Url
             */
            $crawler = $client->request(
                Request::METHOD_GET,
                $urlGenerator->generate('security_login')
            );

            /**
             * Form
             */
            $form = $crawler->filter('form[name=login]')->form([
                '_username' => 'admin@admin.com',
                '_password' => 'password'
            ]);

            $client->submit($form);

            /**
             * Login
             */
            $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

            /**
             * Redirection
             */
            $client->followRedirect();

            $this->assertRouteSame('post_index');

            $this->assertResponseIsSuccessful();
            $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        }

        /**
         * Test Login with Bad Credentials
         */
        public function testLoginWithBadCredentials(): void
        {
            $client = static::createClient();

            /** @var UrlGeneratorInterface */
            $urlGenerator = $client->getContainer()->get('router');

            /**
             * Url
             */
            $crawler = $client->request(
                Request::METHOD_GET,
                $urlGenerator->generate('security_login')
            );

            /**
             * Form
             */
            $form = $crawler->filter('form[name=login]')->form([
                '_username' => 'admin@admin.com',
                '_password' => 'p'
            ]);

            $client->submit($form);

            /**
             * Login
             */
            $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

            /**
             * Redirection
             */
            $client->followRedirect();

            $this->assertRouteSame('security_login');

            $this->assertResponseIsSuccessful();
            $this->assertResponseStatusCodeSame(Response::HTTP_OK);

            /**
             * Error Message
             */
            $this->assertSelectorExists('div.errorLogin');
            $this->assertSelectorTextContains('div.errorLogin', 'Identifiants invalides.');
        }

        /**
         * Test Logout
         */
        public function testLogoutWorks(): void
        {
            $client = static::createClient();

            /** @var UserRepository */
            $userRepository = $client->getContainer()->get(UserRepository::class);

            /** @var UrlGeneratorInterface */
            $urlGenerator = $client->getContainer()->get('router');

            /** @var User */
            $user = $userRepository->findOneBy([]);

            $client->loginUser($user);

            /**
             * Url
             */
            $client->request(
                Request::METHOD_GET,
                $urlGenerator->generate('security_logout')
            );

             /**
             * Logout
             */
            $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

            /**
             * Redirection
             */
            $client->followRedirect();

            $this->assertRouteSame('post_index');

            $this->assertResponseIsSuccessful();
            $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        }

        /**
         * Test Remember Me
         */
        public function testLoginWithRememberMe(): void
        {
            $client = static::createClient();

            /** @var UrlGeneratorInterface */
            $urlGenerator = $client->getContainer()->get('router');

            /** Test Not Has Cookie */
            $this->assertBrowserNotHasCookie('REMEMBERME');

            /**
             * Url
             */
            $crawler = $client->request(
                Request::METHOD_GET,
                $urlGenerator->generate('security_login')
            );

            /**
             * Form
             */
            $form = $crawler->filter('form[name=login]')->form([
                '_username' => 'admin@admin.com',
                '_password' => 'password',
                '_remember_me' => 'on'
            ]);

            $client->submit($form);

            /**
             * Login
             */
            $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

            /**
             * Redirection
             */
            $client->followRedirect();

            $this->assertRouteSame('post_index');

            $this->assertResponseIsSuccessful();
            $this->assertResponseStatusCodeSame(Response::HTTP_OK);

            /** Test Has Cookie */
            $this->assertBrowserHasCookie('REMEMBERME');
        }
    }