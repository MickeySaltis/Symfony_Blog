<?php

namespace App\Controller;

use App\Entity\Post\Post;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LikeController extends AbstractController
{
    #[Route('/like/article/{id}', name: 'like_post')]
    #[IsGranted('ROLE_USER')]
    public function like(Post $post, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();

        /**
         * If the post has already been liked by the user (Delete like) otherwise add the user's like
         */
        if($post->isLikedByUser($user))
        {
            $post->removeLike($user);
            $manager->flush();

            return $this->json([
                'message' => 'Vous avez disliké cet article.',
                'nbLike' => $post->howManyLikes(),
            ]);
        }
        else
        {
            $post->addLike($user);
            $manager->flush();

            return $this->json([
                'message' => 'Vous avez liké cet article.',
                'nbLike' => $post->howManyLikes(),
            ]);
        }

    }
}