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

#[Route(path: '/profile/oauth/github')]
class GitHubController extends AbstractController
{

    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    /**
     * @param ClientRegistry $clientRegistry
     * @return Response
     */
    #[Route(path: '/attach', name: 'profile.oauth.github')]
    public function connect(ClientRegistry $clientRegistry): Response
    {
        return $clientRegistry
            ->getClient('github_attach')
            ->redirect(['user']);
    }

    /**
     * @param ClientRegistry $clientRegistry
     * @param Handler $handler
     * @return Response
     */
    #[Route(path: '/check', name: 'profile.oauth.github_check')]
    public function check(ClientRegistry $clientRegistry, Handler $handler): Response
    {
        $client = $clientRegistry->getClient('github_attach');

        $command = new Command(
            $this->getUser()->getId(),
            'github',
            $client->fetchUser()->getId()
        );

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Github is successfully attached.');
            return $this->redirectToRoute('profile');
        } catch (\DomainException $e) {
            $this->logger->warning($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('profile');
        }
    }
}
