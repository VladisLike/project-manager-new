<?php
declare(strict_types=1);

namespace App\Controller\Profile\OAuth;

use App\Model\User\UseCase\Network\Detach\Command;
use App\Model\User\UseCase\Network\Detach\Handler;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/profile/oauth')]
class DetachController extends AbstractController
{

    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    /**
     * @param Request $request
     * @param string $network
     * @param string $identity
     * @param Handler $handler
     * @return Response
     */
    #[Route(path: '/detach/{network}/{identity}', name: 'profile.oauth.detach', methods: ['POST'])]
    public function detach(Request $request, string $network, string $identity, Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('delete' . $identity, $request->request->get('_token'))) {
            return $this->redirectToRoute('profile');
        }

        $command = new Command(
            $this->getUser()->getId(),
            $network,
            $identity
        );

        try {
            $handler->handle($command);
            return $this->redirectToRoute('profile');
        } catch (\DomainException $e) {
            $this->logger->warning($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('profile');
        }
    }
}