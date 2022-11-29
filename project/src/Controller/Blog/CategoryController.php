<?php

namespace App\Controller\Blog;

use App\Form\SearchType;
use App\Model\SearchData;
use App\Entity\Post\Category;
use App\Repository\Post\PostRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route("/categories")]
class CategoryController extends AbstractController
{
    /**
     * Controller according to category
     */
    #[Route("/{slug}", name: "category_index", methods: ["GET"])]
    public function index(
        Category $category,
        PostRepository $postRepository,
        Request $request,
    ): Response
    {
        /**
         * Data: Posts by Category
         * Pagination: 9 Posts per page
         */
        $posts = $postRepository->findPublished($request->query->getInt('page', 1), $category);

        /**
         * Form
         */
        $searchData = new SearchData();
        $form = $this->createForm(SearchType::class, $searchData);

        /**
         * Search with the form
         */
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
                $searchData->page = $request->query->getInt('page', 1);
                $posts = $postRepository->findBySearch($searchData);

                return $this->render('pages/blog/index.html.twig', [
                    'category' => $category,
                    'form' => $form->createView(),
                    'posts' => $posts
                ]);
        }

        return $this->render('pages/category/index.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
            'posts' => $posts,
        ]);
    }
}