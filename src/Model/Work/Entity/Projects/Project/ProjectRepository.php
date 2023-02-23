<?php
declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Project;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\EntityRepository;

class ProjectRepository
{
    private EntityRepository $repository;

    public function __construct(
        private readonly EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(Project::class);
    }

    public function get(Id $id): Project
    {
        /** @var Project $project */
        if (!$project = $this->repository->find($id->getValue())) {
            throw new EntityNotFoundException('Project is not found.');
        }
        return $project;
    }

    public function add(Project $project): void
    {
        $this->entityManager->persist($project);
    }

    public function remove(Project $project): void
    {
        $this->entityManager->remove($project);
    }
}