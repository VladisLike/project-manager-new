<?php
declare(strict_types=1);

namespace App\ReadModel\Work\Members;

use Doctrine\DBAL\Connection;

class GroupFetcher
{

    public function __construct(
        private readonly Connection $connection)
    {
    }

    public function all(): array
    {
        return $this->connection->createQueryBuilder()
            ->select(
                'g.id',
                'g.name',
                '(SELECT COUNT(*) FROM work_members_members m WHERE m.group_id = g.id) AS members'
            )
            ->from('work_members_groups', 'g')
            ->orderBy('name')
            ->executeQuery()->fetchAllAssociative();
    }

    public function assoc(): array
    {
        return $this->connection->createQueryBuilder()
            ->select(
                'id',
                'name'
            )
            ->from('work_members_groups')
            ->orderBy('name')
            ->executeQuery()->fetchAllKeyValue();
    }
}