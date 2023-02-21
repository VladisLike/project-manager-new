<?php
declare(strict_types=1);

namespace App\Security\OAuth;

use App\Model\User\UseCase\Network\Auth\Handler;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

abstract class AbstractSocialAuthenticator extends OAuth2Authenticator implements AuthenticationEntrypointInterface
{
    public const LOGIN_ROUTE = 'app_login';

    public function __construct(
        protected readonly ClientRegistry         $clientRegistry,
        protected readonly EntityManagerInterface $entityManager,
        protected readonly RouterInterface        $router,
        protected readonly UserProviderInterface  $userProvider,
        protected readonly Handler                $handler,
        protected readonly UrlGeneratorInterface  $urlGenerator)
    {
    }

}