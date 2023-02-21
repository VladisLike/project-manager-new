<?php
declare(strict_types=1);

namespace App\Model\User\UseCase\Reset\Request;

use App\Model\Flusher;
use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Service\Mailer\MailerSenderInterface;
use App\Model\User\Service\Tokenizer\ResetTokenizer;

class Handler
{

    /**
     * @param UserRepository $users
     * @param ResetTokenizer $tokenizer
     * @param Flusher $flusher
     * @param MailerSenderInterface $sender
     */
    public function __construct(
        private readonly UserRepository        $users,
        private readonly ResetTokenizer        $tokenizer,
        private readonly Flusher               $flusher,
        private readonly MailerSenderInterface $sender)
    {
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