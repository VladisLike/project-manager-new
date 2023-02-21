<?php
declare(strict_types=1);

namespace App\Model\User\Service\Tokenizer;

use App\Model\User\Entity\User\ResetToken;
use DateInterval;
use Ramsey\Uuid\Uuid;

class ResetTokenizer
{
    private DateInterval $interval;

    /**
     * @param DateInterval $interval
     */
    public function __construct(DateInterval $interval)
    {
        $this->interval = $interval;
    }


    public function generate(): ResetToken
    {
        return new ResetToken(
            Uuid::uuid4()->toString(),
            (new \DateTimeImmutable())->add($this->interval)
        );
    }
}