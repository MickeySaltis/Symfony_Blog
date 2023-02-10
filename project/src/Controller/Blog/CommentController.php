<?php

namespace App\Controller\Blog;

use App\Entity\Post\Comment;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentController extends AbstractController
{
    #[Route('/comment/{id}', name: 'comment_delete')]
    #[Security("is_granted('ROLE_USER') and user === comment.getAuthor()")]
    public function delete(Comment $comment, EntityManagerInterface $manager, Request $request): Response
    {
        $params = ['slug' => $comment->getPost()->getSlug()];
        
        if($this->isCsrfTokenValid('delete' . $comment->getId(), $request->request->get('_token')))
        {
            $manager->remove($comment);
            $manager->flush();
        }

        $this->addFlash('success', 'Votre commentaire a bien été supprimé.');

        return $this->redirectToRoute('post_show', $params);
    }
}