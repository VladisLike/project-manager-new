<?php
declare(strict_types=1);

namespace App\ReadModel\Work\Projects\Project\Filter;

use App\Model\Work\Entity\Members\Member\Status;

class Filter
{
    public ?string $name = null;
    public ?string $status = Status::ACTIVE;
}