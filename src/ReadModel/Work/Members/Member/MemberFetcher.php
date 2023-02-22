<?php
declare(strict_types=1);

namespace App\ReadModel\Work\Members\Member;

use App\Model\Work\Entity\Members\Member\Member;
use App\ReadModel\Work\Members\Member\Filter\Filter;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class MemberFetcher
{
    private EntityRepository $repository;

    public function __construct(
        private readonly Connection             $connection,
        private readonly EntityManagerInterface $entityManager,
        private readonly PaginatorInterface     $paginator)
    {
        $this->repository = $entityManager->getRepository(Member::class);
    }

    public function find(string $id): ?Member
    {
        return $this->repository->find($id);
    }

    /**
     * @param Filter $filter
     * @param int $page
     * @param int $size
     * @param string $sort
     * @param string $direction
     * @return PaginationInterface
     */
    public function all(Filter $filter, int $page, int $size, string $sort, string $direction): PaginationInterface
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'm.id',
                'TRIM(CONCAT(m.name_first, \' \', m.name_last)) AS name',
                'm.email',
                'g.name as group',
                'm.status'
            )
            ->from('work_members_members', 'm')
            ->innerJoin('m', 'work_members_groups', 'g', 'm.group_id = g.id');

        if ($filter->name) {
            $like = '%' . mb_strtolower($filter->name) . '%';
            $qb->andWhere($qb->expr()->like('LOWER(CONCAT(m.name_first, \' \', m.name_last))', "'$like'"));
        }

        if ($filter->email) {
            $like = '%' . mb_strtolower($filter->email) . '%';
            $qb->andWhere($qb->expr()->like('LOWER(m.email)', "'$like'"));
        }

        if ($filter->status) {
            $qb->andWhere("m.status = '$filter->status'");
        }

        if ($filter->group) {
            $qb->andWhere("m.group_id = '$filter->group'");
        }

        if (!\in_array($sort, ['name', 'email', 'group', 'status'], true)) {
            throw new \UnexpectedValueException('Cannot sort by ' . $sort);
        }

        $qb->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc');

        return $this->paginator->paginate($qb, $page, $size);
    }

    public function exists(string $id): bool
    {
        return $this->connection->createQueryBuilder()
                ->select('COUNT (id)')
                ->from('work_members_members')
                ->where('id = :id')
                ->setParameter(':id', $id)
                ->executeQuery()->fetchFirstColumn() > 0;
    }
}