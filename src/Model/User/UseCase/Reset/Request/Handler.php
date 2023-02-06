<?php
declare(strict_types=1);

namespace App\Model\User\UseCase\Reset\Request;

use App\Model\Flusher;
use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Service\Mailer\MailerSenderInterface;
use App\Model\User\Service\ResetTokenizer;

class Handler
{
    private UserRepository $users;
    private ResetTokenizer $tokenizer;
    private Flusher $flusher;
    private MailerSenderInterface $sender;

    /**
     * @param UserRepository $users
     * @param ResetTokenizer $tokenizer
     * @param Flusher $flusher
     * @param MailerSenderInterface $sender
     */
    public function __construct(UserRepository $users, ResetTokenizer $tokenizer, Flusher $flusher, MailerSenderInterface $sender)
    {
        $this->users = $users;
        $this->tokenizer = $tokenizer;
        $this->flusher = $flusher;
        $this->sender = $sender;
    }

    public function handle(Command $command): void
    {
        $user = $this->users->getByEmail(new Email($command->email));

        $user->requestPasswordReset(
            $this->tokenizer->generate(),
            new \DateTimeImmutable('now')
        );

        $this->flusher->flush();

        $this->sender->sendResetToken($user->getEmail(), $user->getResetToken());
    }


}