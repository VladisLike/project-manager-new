<?php
declare(strict_types=1);

namespace App\ReadModel\User;

class ShortView
{
    public string $id;
    public string $email;
    public string $role;
    public string $status;

    /**
     * @param string $id
     * @param string $email
     * @param string $role
     * @param string $status
     */
    public function __construct(string $id, string $email, string $role, string $status)
    {
        $this->id = $id;
        $this->email = $email;
        $this->role = $role;
        $this->status = $status;
    }


}