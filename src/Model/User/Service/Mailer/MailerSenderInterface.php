<?php
declare(strict_types=1);

namespace App\Model\User\Service\Mailer;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\ResetToken;

interface MailerSenderInterface
{
    /**
     * @param Email $email
     * @param ResetToken $token
     *
     * @return void
     */
    public function sendResetToken(Email $email, ResetToken $token): void;

    /**
     * @param Email $email
     * @param string $token
     *
     * @return void
     */
    public function sendConfirmToken(Email $email, string $token): void;

    /**
     * @param Email $email
     * @param string $token
     *
     * @return void
     */
    public function sendNewEmailConfirmToken(Email $email, string $token): void;


}