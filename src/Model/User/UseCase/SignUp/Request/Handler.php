<?php
declare(strict_types=1);

namespace App\Model\User\UseCase\SignUp\Request;

use App\Model\Flusher;
use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\User;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Service\Mailer\MailerSenderInterface;
use App\Model\User\Service\PasswordHasher;
use App\Model\User\Service\SignUpConfirmTokenizer;

class Handler
{

    private UserRepository $users;
    private PasswordHasher $hasher;
    private SignUpConfirmTokenizer $tokenizer;
    private MailerSenderInterface $sender;
    private Flusher $flusher;

    /**
     * @param UserRepository $users
     * @param PasswordHasher $hasher
     * @param SignUpConfirmTokenizer $tokenizer
     * @param MailerSenderInterface $sender
     * @param Flusher $flusher
     */
    public function __construct(
        UserRepository         $users,
        PasswordHasher         $hasher,
        SignUpConfirmTokenizer $tokenizer,
        MailerSenderInterface  $sender,
        Flusher                $flusher)
    {
        $this->users = $users;
        $this->hasher = $hasher;
        $this->tokenizer = $tokenizer;
        $this->sender = $sender;
        $this->flusher = $flusher;
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
            $email,
            $this->hasher->hash($command->password),
            $token = $this->tokenizer->generate()
        );

        $this->users->add($user);

        $this->sender->sendConfirmToken($email, $token);

        $this->flusher->flush();
    }

}