<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * Login
     */
    #[Route('/login', name: 'security_login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $utils): Response
    {
        /**
         * Error
         */
        $error = $utils->getLastAuthenticationError();
        if($error)
        {
            $this->addFlash('warning', $error);
        }
        
        $lastUserName = $utils->getLastUserName();

        return $this->render('pages/security/login.html.twig', [
            // 'error' => $error,
            'last_userName' => $lastUserName,
        ]);
    }

    /**
     * Logout
     */
    #[Route('/logout', name: 'security_logout', methods: ['GET'])]
    public function logout(): void
    {}
}