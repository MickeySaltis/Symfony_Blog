<?php

namespace App\Controller\Blog;

use App\Entity\Post\Post;
use App\Repository\Post\PostRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class PostController extends AbstractController
{
    /**
     * Home: Displaying Posts
     */
    #[Route('/', name:'post_index', methods: ['GET'])]
    public function index(
        PostRepository $postRepository,
        Request $request
    ): Response
    {

        /**
         * Data: Posts
         * Pagination: 9 Posts per page
         */
        $posts = $postRepository->findPublished($request->query->getInt('page', 1));
        
        
        return $this->render('pages/blog/index.html.twig', [
            'posts' => $posts,
        ]);
    }


    /**
     * Post: Detail
     * ParamConverter: Post & slug
     */
    #[Route('/article/{slug}', name: 'post_show', methods: 'GET')]
    public function show(
        Post $post,
    ): Response
    {
        return $this->render('pages/blog/show.html.twig', [
            'post' => $post,
        ]);
    }
}