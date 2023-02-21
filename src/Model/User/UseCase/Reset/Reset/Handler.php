<?php
declare(strict_types=1);

namespace App\Model\User\UseCase\Reset\Reset;

use App\Model\Flusher;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Service\Password\PasswordHasher;

class Handler
{
    /**
     * @param UserRepository $users
     * @param PasswordHasher $hasher
     * @param Flusher $flusher
     */
    public function __construct(
        private readonly UserRepository $users,
        private readonly PasswordHasher $hasher,
        private readonly Flusher        $flusher)
    {
    }

    public function handle(Command $command): void
    {
        if (!$user = $this->users->findByResetToken($command->token)) {
            throw new \DomainException('Incorrect or confirmed token.');
        }

        $user->passwordReset(
            new \DateTimeImmutable('now'),
            $this->hasher->hash($command->password)
        );

        $this->flusher->flush();
    }


}