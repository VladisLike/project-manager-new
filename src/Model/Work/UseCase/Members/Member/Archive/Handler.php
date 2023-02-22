<?php
declare(strict_types=1);

namespace App\Model\Work\UseCase\Members\Member\Archive;

use App\Model\Flusher;
use App\Model\Work\Entity\Members\Member\Id;
use App\Model\Work\Entity\Members\Member\MemberRepository;

class Handler
{
    public function __construct(
        private readonly MemberRepository $members,
        private readonly Flusher          $flusher)
    {
    }

    public function handle(Command $command): void
    {
        $member = $this->members->get(new Id($command->id));

        $member->archive();

        $this->flusher->flush();
    }
}