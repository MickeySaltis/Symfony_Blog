<?php

namespace App\Controller\Blog;

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
    #[Route('/', name:'post.index', methods: ['GET'])]
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
}