<?php
declare(strict_types=1);

namespace App\Model\User\Entity\User;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class UserRepository
{

    private EntityRepository $repository;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(private readonly EntityManagerInterface $entityManager,)
    {
        $this->repository = $entityManager->getRepository(User::class);
    }

    /**
     * @param string $token
     * @return User|null
     */
    public function findByConfirmToken(string $token): ?User
    {
        return $this->repository->findOneBy(['confirmToken' => $token]);
    }

    /**
     * @param string $token
     * @return User|null
     */
    public function findByResetToken(string $token): ?User
    {
        return $this->repository->findOneBy(['resetToken.token' => $token]);
    }


    /**
     * @param Id $id
     * @return User
     *
     * @throws EntityNotFoundException
     */
    public function get(Id $id): User
    {
        /** @var User $user */
        if (!$user = $this->repository->find($id->getValue())) {
            throw new EntityNotFoundException('User is not found.');
        }
        return $user;
    }


    /**
     * @param Email $email
     * @return User
     *
     * @throws EntityNotFoundException
     */
    public function getByEmail(Email $email): User
    {
        if (!$user = $this->repository->findOneBy(['email' => $email->getValue()])) {
            throw new EntityNotFoundException('User is not found.');
        }
        return $user;
    }


    /**
     * @param Email $email
     * @return bool
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function hasByEmail(Email $email): bool
    {
        return $this->repository->createQueryBuilder('t')
                ->select('COUNT(t.id)')
                ->andWhere('t.email = :email')
                ->setParameter(':email', $email->getValue())
                ->getQuery()->getSingleScalarResult() > 0;
    }


    /**
     * @param string $network
     * @param string $identity
     * @return bool
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function hasByNetworkIdentity(string $network, string $identity): bool
    {
        return $this->repository->createQueryBuilder('t')
                ->select('COUNT(t.id)')
                ->innerJoin('t.networks', 'n')
                ->andWhere('n.network = :network and n.identity = :identity')
                ->setParameter(':network', $network)
                ->setParameter(':identity', $identity)
                ->getQuery()->getSingleScalarResult() > 0;
    }

    /**
     * @param User $user
     * @return void
     */
    public function add(User $user): void
    {
        $this->entityManager->persist($user);
    }

}