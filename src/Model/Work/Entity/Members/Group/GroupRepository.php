<?php
declare(strict_types=1);

namespace App\Model\Work\Entity\Members\Group;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\EntityRepository;

class GroupRepository
{
    private EntityRepository $repository;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(Group::class);
    }

    public function get(Id $id): Group
    {
        /** @var Group $group */
        if (!$group = $this->repository->find($id->getValue())) {
            throw new EntityNotFoundException('Group is not found.');
        }
        return $group;
    }

    public function add(Group $group): void
    {
        $this->entityManager->persist($group);
    }

    public function remove(Group $group): void
    {
        $this->entityManager->remove($group);
    }
}