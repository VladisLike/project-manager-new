<?php
declare(strict_types=1);

namespace App\Controller\Auth\OAuth;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FacebookController extends AbstractController
{
    /**
     * @param ClientRegistry $clientRegistry
     * @return Response
     */
    #[Route(path: '/oauth/facebook', name: 'oauth.facebook')]
    public function connect(ClientRegistry $clientRegistry): Response
    {
        return $clientRegistry
            ->getClient('facebook_main')
            ->redirect(['public_profile', 'email']);
    }

    /**
     * @return Response
     */
    #[Route(path: '/oauth/facebook/check', name: 'oauth.facebook_check')]
    public function check(): Response
    {
        return $this->redirectToRoute('home');
    }
}