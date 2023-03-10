<?php
declare(strict_types=1);

namespace App\Model\User\UseCase\Network\Auth;

use App\Model\Flusher;
use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Name;
use App\Model\User\Entity\User\User;
use App\Model\User\Entity\User\UserRepository;
use Symfony\Component\Security\Core\User\UserInterface;

class Handler
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly Flusher        $flusher)
    {
    }

    public function handle(Command $command): void
    {
        if ($this->users->hasByNetworkIdentity($command->network, $command->identity)) {
            throw new \DomainException('User already exists.');
        }

        $user = User::signUpByNetwork(
            Id::next(),
            new \DateTimeImmutable('now'),
            new Name(
                $command->firstName,
                $command->lastName
            ),
            $command->network,
            $command->identity
        );

        $this->users->add($user);

        $this->flusher->flush();
    }

}