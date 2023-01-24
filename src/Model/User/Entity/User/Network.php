<?php
declare(strict_types=1);

namespace App\Model\User\Entity\User;

use Ramsey\Uuid\Uuid;

class Network
{
    private string $id;

    private User $user;

    private string $network;

    private string $identity;

    /**
     * @param User $user
     * @param string $network
     * @param string $identity
     */
    public function __construct(User $user, string $network, string $identity)
    {
        $this->id = Uuid::uuid4()->toString();;
        $this->user = $user;
        $this->network = $network;
        $this->identity = $identity;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function isForNetwork(string $network): bool
    {
        return $this->network === $network;
    }

    public function getNetwork(): string
    {
        return $this->network;
    }

    public function getIdentity(): string
    {
        return $this->identity;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

}