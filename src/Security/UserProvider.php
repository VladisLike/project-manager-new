<?php
declare(strict_types=1);

namespace App\Security;

use App\ReadModel\User\AuthView;
use App\ReadModel\User\UserFetcher;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{

    public function __construct(private readonly UserFetcher $users)
    {
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = $this->loadUser($identifier);
        return self::identityByUser($user, $identifier);
    }

    public function refreshUser(UserInterface $identity): UserInterface
    {
        $user = $this->loadUser($identity->getUsername());
        return self::identityByUser($user, $identity->getUsername());
    }

    public function supportsClass($class): bool
    {
        return $class === UserIdentity::class;
    }

    private function loadUser($username): AuthView
    {
        $chunks = explode(':', $username);

        if (\count($chunks) === 2 && $user = $this->users->findForAuthByNetwork($chunks[0], $chunks[1])) {
            return $user;
        }

        if ($user = $this->users->findForAuthByEmail($username)) {
            return $user;
        }

        throw new EntityNotFoundException('');
    }

    private static function identityByUser(AuthView $user, string $username): UserIdentity
    {
        return new UserIdentity(
            $user->id,
            $username,
            $user->password_hash ?: '',
            $user->role,
            $user->status
        );
    }
}