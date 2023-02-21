<?php
declare(strict_types=1);

namespace App\Security\OAuth;

use App\Model\User\UseCase\Network\Auth\Command;
use League\OAuth2\Client\Provider\GithubResourceOwner;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class GitHubAuthenticator extends AbstractSocialAuthenticator
{
    use TargetPathTrait;

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'oauth.github_check';
    }

    public function start(Request $request, AuthenticationException $authException = null): RedirectResponse
    {
        return new RedirectResponse(
            '/oauth/github',
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }

    public function authenticate(Request $request): Passport
    {
        $client = $this->clientRegistry->getClient('github_main');
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function () use ($accessToken, $client) {
                /** @var GithubResourceOwner $gitHubUser */
                $gitHubUser = $client->fetchUserFromToken($accessToken);

                $network = 'github';
                $id = $gitHubUser->getId();
                $username = $network . ':' . $id;

                $command = new Command($network, (string)$id);
                $command->firstName = $gitHubUser->getName();
                $command->lastName = $gitHubUser->getNickname();
//                $command->email = $gitHubUser->getEmail();

                try {
                    return $this->userProvider->loadUserByUsername($username);
                } catch (UserNotFoundException) {
                    $this->handler->handle($command);
                    return $this->userProvider->loadUserByUsername($username);
                }
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('home'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new RedirectResponse($this->urlGenerator->generate(self::LOGIN_ROUTE));
    }

}