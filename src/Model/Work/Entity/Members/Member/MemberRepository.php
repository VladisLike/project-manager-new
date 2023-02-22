<?php
declare(strict_types=1);

namespace App\Model\Work\Entity\Members\Member;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use App\Model\Work\Entity\Members\Group\Id as GroupId;
use Doctrine\ORM\EntityRepository;

class MemberRepository
{
    private EntityRepository $repository;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(Member::class);
    }

    public function has(Id $id): bool
    {
        return $this->repository->createQueryBuilder('t')
                ->select('COUNT(t.id)')
                ->andWhere('t.id = :id')
                ->setParameter(':id', $id->getValue())
                ->getQuery()->getSingleScalarResult() > 0;
    }

    public function hasByGroup(GroupId $id): bool
    {
        return $this->repository->createQueryBuilder('t')
                ->select('COUNT(t.id)')
                ->andWhere('t.group = :group')
                ->setParameter(':group', $id->getValue())
                ->getQuery()->getSingleScalarResult() > 0;
    }

    public function get(Id $id): Member
    {
        /** @var Member $member */
        if (!$member = $this->repository->find($id->getValue())) {
            throw new EntityNotFoundException('Member is not found.');
        }
        return $member;
    }

    public function add(Member $member): void
    {
        $this->entityManager->persist($member);
    }
}