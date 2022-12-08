<?php

namespace App\Controller\Blog;

use App\Form\SearchType;
use App\Entity\Post\Post;
use App\Model\SearchData;
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

        /**
         * Form
         */
        $searchData = new SearchData();
        $form = $this->createForm(SearchType::class, $searchData);

        /**
         * Search with the form
         */
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $searchData->page = $request->query->getInt('page', 1);
            $posts = $postRepository->findBySearch($searchData);

            return $this->render('pages/blog/index.html.twig', [
                // 'category' => $category,
                'form' => $form->createView(),
                'posts' => $posts
            ]);
        }
        
        return $this->render('pages/blog/index.html.twig', [
            'posts' => $posts,
            'form' => $form->createView(),
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