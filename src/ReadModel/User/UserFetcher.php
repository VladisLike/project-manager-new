<?php
declare(strict_types=1);

namespace App\ReadModel\User;

use App\Model\User\Entity\User\User;
use App\ReadModel\NotFoundException;
use App\ReadModel\User\Filter\Filter;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class UserFetcher
{
    private EntityRepository $repository;

    public function __construct(
        private readonly Connection             $connection,
        private readonly EntityManagerInterface $em,
        private readonly PaginatorInterface     $paginator)
    {
        $this->repository = $em->getRepository(User::class);
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
                'TRIM(CONCAT(name_first, \' \', name_last)) AS name',
                'role',
                'status'
            )
            ->from('user_users')
            ->where("email = '$email'")
            ->executeQuery()->fetchAssociative();


        return $stmt ? $this->intoAuthView($stmt) : null;
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
                'TRIM(CONCAT(u.name_first, \' \', u.name_last)) AS name',
                'u.role',
                'u.status'
            )
            ->from('user_users', 'u')
            ->innerJoin('u', 'user_user_networks', 'n', 'n.user_id = u.id')
            ->andWhere("n.network = '$network'")
            ->andWhere("n.identity = '$identity'")
            ->executeQuery()->fetchAssociative();

        return $stmt ? $this->intoAuthView($stmt) : null;
    }

    /**
     * @param string $email
     *
     * @return ShortView|null
     * @throws Exception
     */
    public function findByEmail(string $email): ?ShortView
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'email',
                'role',
                'status'
            )
            ->from('user_users')
            ->where("email = '$email'")
            ->executeQuery()->fetchAssociative();

        return $stmt ? $this->intoShortView($stmt) : null;
    }

    /**
     * @param string $token
     *
     * @return ShortView|null
     * @throws Exception
     */
    public function findBySignUpConfirmToken(string $token): ?ShortView
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'email',
                'role',
                'status'
            )
            ->from('user_users')
            ->where("confirm_token = '$token'")
            ->executeQuery()->fetchAssociative();

        return $stmt ? $this->intoShortView($stmt) : null;
    }

    public function get(string $id): User
    {
        if (!$user = $this->repository->find($id)) {
            throw new NotFoundException('User is not found');
        }

        return $user;
    }

    public function all(Filter $filter, int $page, int $size, string $sort, string $direction): PaginationInterface
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'date',
                'TRIM(CONCAT(name_first, \' \', name_last)) AS name',
                'email',
                'role',
                'status'
            )
            ->from('user_users');

        if ($filter->name) {
            $qb->andWhere($qb->expr()->like('LOWER(CONCAT(name_first, \' \', name_last))', ':name'));
            $qb->setParameter(':name', '%' . mb_strtolower($filter->name) . '%');
        }

        if ($filter->email) {
            $qb->andWhere($qb->expr()->like('LOWER(email)', ':email'));
            $qb->setParameter(':email', '%' . mb_strtolower($filter->email) . '%');
        }

        if ($filter->status) {
            $qb->andWhere("status = $filter->status");
        }

        if ($filter->role) {
            $qb->andWhere("role = $filter->role");
        }

        if (!\in_array($sort, ['date', 'name', 'email', 'role', 'status'], true)) {
            throw new \UnexpectedValueException('Cannot sort by ' . $sort);
        }

        $qb->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc');

        return $this->paginator->paginate($qb, $page, $size);
    }

    private function intoAuthView($temp): AuthView
    {
        return new AuthView($temp['id'], $temp['email'], $temp['password_hash'], $temp['name'], $temp['role'], $temp['status']);
    }

    private function intoShortView($temp): ShortView
    {
        return new ShortView($temp['id'], $temp['email'], $temp['role'], $temp['status']);
    }

}