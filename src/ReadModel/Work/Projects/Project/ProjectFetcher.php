<?php
declare(strict_types=1);

namespace App\ReadModel\Work\Projects\Project;

use App\ReadModel\Work\Projects\Project\Filter\Filter;
use Doctrine\DBAL\Connection;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use function PHPUnit\Framework\exactly;

class ProjectFetcher
{

    public function __construct(
        private readonly Connection         $connection,
        private readonly PaginatorInterface $paginator)
    {
    }

    public function getMaxSort(): int
    {
        return (int)$this->connection->createQueryBuilder()
            ->select('MAX(p.sort) AS m')
            ->from('work_projects_projects', 'p')
            ->executeQuery()->fetchAssociative()['m'];
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
                'p.id',
                'p.name',
                'p.status'
            )
            ->from('work_projects_projects', 'p');

        if ($filter->name) {
            $like = '%' . mb_strtolower($filter->name) . '%';
            $qb->andWhere($qb->expr()->like('LOWER(p.name)', "'$like'"));
        }

        if ($filter->status) {
            $qb->andWhere("p.status = '$filter->status'");
        }

        if (!\in_array($sort, ['name', 'status'], true)) {
            throw new \UnexpectedValueException('Cannot sort by ' . $sort);
        }

        $qb->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc');

        return $this->paginator->paginate($qb, $page, $size);
    }
}