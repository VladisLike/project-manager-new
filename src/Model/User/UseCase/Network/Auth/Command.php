<?php
declare(strict_types=1);

namespace App\Model\User\UseCase\Network\Auth;

class Command
{
    public string $network;

    public string $identity;

    /**
     * @param string $network
     * @param string $identity
     */
    public function __construct(string $network, string $identity)
    {
        $this->network = $network;
        $this->identity = $identity;
    }

}