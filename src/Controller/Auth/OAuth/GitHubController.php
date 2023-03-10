<?php
declare(strict_types=1);

namespace App\Controller\Auth\OAuth;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GitHubController extends AbstractController
{
    /**
     * @param ClientRegistry $clientRegistry
     * @return Response
     */
    #[Route(path: '/oauth/github', name: 'oauth.github')]
    public function connect(ClientRegistry $clientRegistry): Response
    {
        return $clientRegistry
            ->getClient('github_main')
            ->redirect(['user']);
    }

    /**
     * @return Response
     */
    #[Route(path: '/oauth/github/check', name: 'oauth.github_check')]
    public function check(): Response
    {
        return $this->redirectToRoute('home');
    }

}