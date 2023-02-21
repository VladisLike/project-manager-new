<?php
declare(strict_types=1);

namespace App\Model\User\UseCase\Create;

use App\Model\Flusher;
use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Name;
use App\Model\User\Entity\User\User;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Service\Password\PasswordGenerator;
use App\Model\User\Service\Password\PasswordHasher;

class Handler
{
    public function __construct(
        private readonly UserRepository    $users,
        private readonly PasswordHasher    $hasher,
        private readonly PasswordGenerator $generator,
        private readonly Flusher           $flusher
    )
    {
    }

    public function handle(Command $command): void
    {
        $email = new Email($command->email);

        if ($this->users->hasByEmail($email)) {
            throw new \DomainException('User with this email already exists.');
        }

        $user = User::create(
            Id::next(),
            new \DateTimeImmutable(),
            new Name(
                $command->firstName,
                $command->lastName
            ),
            $email,
            $this->hasher->hash($this->generator->generate())
        );

        $this->users->add($user);

        $this->flusher->flush();
    }

}