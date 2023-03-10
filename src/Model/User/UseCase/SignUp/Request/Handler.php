<?php
declare(strict_types=1);

namespace App\Model\User\UseCase\SignUp\Request;

use App\Model\Flusher;
use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Name;
use App\Model\User\Entity\User\User;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Service\Mailer\MailerSenderInterface;
use App\Model\User\Service\Password\PasswordHasher;
use App\Model\User\Service\Tokenizer\SignUpConfirmTokenizer;

class Handler
{


    /**
     * @param UserRepository $users
     * @param PasswordHasher $hasher
     * @param SignUpConfirmTokenizer $tokenizer
     * @param MailerSenderInterface $sender
     * @param Flusher $flusher
     */
    public function __construct(
        private readonly UserRepository         $users,
        private readonly PasswordHasher         $hasher,
        private readonly SignUpConfirmTokenizer $tokenizer,
        private readonly MailerSenderInterface  $sender,
        private readonly Flusher                $flusher)
    {
    }


    public function handle(Command $command): void
    {
        $email = new Email($command->email);

        if ($this->users->hasByEmail($email)) {
            throw new \DomainException('User already exists.');
        }

        $user = User::signUpByEmail(
            Id::next(),
            new \DateTimeImmutable('now'),
            new Name(
                $command->firstName,
                $command->lastName
            ),
            $email,
            $this->hasher->hash($command->password),
            $token = $this->tokenizer->generate()
        );

        $this->users->add($user);

        $this->sender->sendConfirmToken($email, $token);

        $this->flusher->flush();
    }

}