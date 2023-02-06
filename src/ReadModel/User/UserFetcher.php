<?php
declare(strict_types=1);

namespace App\ReadModel\User;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

class UserFetcher
{
    public function __construct(private readonly Connection $connection)
    {
    }

    /**
     * @throws Exception
     */
    public function existsByResetToken(string $token): bool
    {
        return $this->connection->createQueryBuilder()
                ->select('COUNT (*)')
                ->from('user_users')
                ->where("reset_token_token = '$token'")
                ->fetchOne() > 0;
    }

    /**
     * @throws Exception
     */
    public function findForAuthByEmail(string $email): ?AuthView
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'email',
                'password_hash',
                'role',
                'status'
            )
            ->from('user_users')
            ->where("email = '$email'")
            ->executeQuery()->fetchAssociative();

        return $this->intoAuthView($stmt) ?: null;
    }

    /**
     * @param string $network
     * @param string $identity
     *
     * @return AuthView|null
     * @throws Exception
     */
    public function findForAuthByNetwork(string $network, string $identity): ?AuthView
    {

        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'u.id',
                'u.email',
                'u.password_hash',
                'u.role',
                'u.status'
            )
            ->from('user_users', 'u')
            ->innerJoin('u', 'user_user_networks', 'n', 'n.user_id = u.id')
            ->where('n.network = :network AND n.identity = :identity')
            ->setParameter(':network', $network)
            ->setParameter(':identity', $identity)
            ->executeQuery()->fetchAssociative();

        dump($stmt);
        exit();

        return $this->intoAuthView($stmt) ?: null;
    }

    private function intoAuthView(array $temp): AuthView
    {
        return new AuthView($temp['id'], $temp['email'], $temp['password_hash'], $temp['role'], $temp['status']);
    }

}