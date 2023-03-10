<?php
declare(strict_types=1);

namespace App\Model\User\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

#[ORM\Embeddable]
class ResetToken
{
    #[ORM\Column(type: "string", nullable: true)]
    private string $token;

    #[ORM\Column(type: "datetime_immutable", nullable: true)]
    private ?\DateTimeImmutable $expires = null;

    /**
     * @param string $token
     * @param \DateTimeImmutable $expires
     */
    public function __construct(string $token, \DateTimeImmutable $expires)
    {
        Assert::notEmpty($token);
        $this->token = $token;
        $this->expires = $expires;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param \DateTimeImmutable $date
     * @return bool
     */
    public function isExpiredTo(\DateTimeImmutable $date): bool
    {
        return $this->expires <= $date;
    }

    /**
     * @return bool
     * @internal for postLoad callback
     */
    public function isEmpty(): bool
    {
        return empty($this->token);
    }


}