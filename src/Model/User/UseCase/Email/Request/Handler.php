<?php
declare(strict_types=1);

namespace App\Model\User\UseCase\Email\Request;

use App\Model\Flusher;
use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Service\Mailer\MailerSenderInterface;
use App\Model\User\Service\Tokenizer\NewEmailConfirmTokenizer;

class Handler
{

    public function __construct(
        private readonly UserRepository           $users,
        private readonly NewEmailConfirmTokenizer $tokenizer,
        private readonly MailerSenderInterface    $sender,
        private readonly Flusher                  $flusher
    )
    {
    }

    public function handle(Command $command): void
    {
        $user = $this->users->get(new Id($command->id));

        $email = new Email($command->email);

        if ($this->users->hasByEmail($email)) {
            throw new \DomainException('Email is already in use.');
        }

        $user->requestEmailChanging(
            $email,
            $token = $this->tokenizer->generate()
        );

        $this->flusher->flush();

        $this->sender->sendNewEmailConfirmToken($email, $token);
    }

}