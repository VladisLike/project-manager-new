<?php
declare(strict_types=1);

namespace App\Controller\Profile\OAuth;

use App\Model\User\UseCase\Network\Attach\Command;
use App\Model\User\UseCase\Network\Attach\Handler;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/profile/oauth/facebook')]
class FacebookController extends AbstractController
{

    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    /**
     * @param ClientRegistry $clientRegistry
     * @return Response
     */
    #[Route(path: '/attach', name: 'profile.oauth.facebook')]
    public function connect(ClientRegistry $clientRegistry): Response
    {
        return $clientRegistry
            ->getClient('facebook_attach')
            ->redirect(['public_profile', 'email']);
    }

    /**
     * @Route("/check", name="profile.oauth.facebook_check")
     * @param ClientRegistry $clientRegistry
     * @param Handler $handler
     * @return Response
     */
    #[Route(path: '/check', name: 'profile.oauth.facebook_check')]
    public function check(ClientRegistry $clientRegistry, Handler $handler): Response
    {
        $client = $clientRegistry->getClient('facebook_attach');

        $command = new Command(
            $this->getUser()->getId(),
            'facebook',
            $client->fetchUser()->getId()
        );

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Facebook is successfully attached.');
            return $this->redirectToRoute('profile');
        } catch (\DomainException $e) {
            $this->logger->warning($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('profile');
        }
    }
}