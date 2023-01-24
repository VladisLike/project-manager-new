<?php
declare(strict_types=1);

namespace App\Model\User\Service;

class PasswordHasher
{
    public function hash(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }
}