<?php
declare(strict_types=1);

namespace App\ReadModel\User;

class AuthView
{
    public string $id;
    public string $email;
    public string $password_hash;
    public string $role;
    public string $status;

    public function __construct(string $id, string $email, string $password_hash, string $role, string $status)
    {
        $this->id = $id;
        $this->email = $email;
        $this->password_hash = $password_hash;
        $this->role = $role;
        $this->status = $status;
    }


}