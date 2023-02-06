<?php
declare(strict_types=1);

namespace App\Model\User\Service\Mailer;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\ResetToken;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class MailerSender implements MailerSenderInterface
{
    public const SENDER_NAME = 'Manager';

    private string $mailSenderFrom;

    /**
     * @param MailerInterface $mailer
     */
    public function __construct(private readonly MailerInterface $mailer)
    {
    }

    /**
     * @param Email $email
     * @param string $token
     *
     * @return void
     * @throws TransportExceptionInterface
     */
    public function sendConfirmToken(Email $email, string $token): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address($this->mailSenderFrom, self::SENDER_NAME))
            ->to($email->getValue())
            ->subject('Sig Up Confirmation')
            ->htmlTemplate('mail/user/signup.html.twig')
            ->context([
                'token' => $token
            ]);

        $this->mailer->send($email);
    }

    /**
     * @param Email $email
     * @param ResetToken $token
     *
     * @return void
     * @throws TransportExceptionInterface
     */
    public function sendResetToken(Email $email, ResetToken $token): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address($this->mailSenderFrom, self::SENDER_NAME))
            ->to($email->getValue())
            ->subject('Password resetting')
            ->htmlTemplate('mail/user/reset.html.twig')
            ->context([
                'token' => $token->getToken()
            ]);

        $this->mailer->send($email);
    }

    /**
     * @param string $mailSenderFrom
     *
     * @return void
     */
    public function setMailSenderFrom(string $mailSenderFrom): void
    {
        $this->mailSenderFrom = $mailSenderFrom;
    }

}