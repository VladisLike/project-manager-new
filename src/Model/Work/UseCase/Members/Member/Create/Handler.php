<?php
declare(strict_types=1);

namespace App\Model\Work\UseCase\Members\Member\Create;

use App\Model\Flusher;
use App\Model\Work\Entity\Members\Group\GroupRepository;
use App\Model\Work\Entity\Members\Group\Id as GroupId;
use App\Model\Work\Entity\Members\Member\Email;
use App\Model\Work\Entity\Members\Member\Member;
use App\Model\Work\Entity\Members\Member\Id;
use App\Model\Work\Entity\Members\Member\MemberRepository;
use App\Model\Work\Entity\Members\Member\Name;

class Handler
{

    public function __construct(
        private readonly MemberRepository $members,
        private readonly GroupRepository  $groups,
        private readonly Flusher          $flusher)
    {
    }

    public function handle(Command $command): void
    {
        $id = new Id($command->id);

        if ($this->members->has($id)) {
            throw new \DomainException('Member already exists.');
        }

        $group = $this->groups->get(new GroupId($command->group));

        $member = new Member(
            $id,
            $group,
            new Name(
                $command->firstName,
                $command->lastName
            ),
            new Email($command->email)
        );

        $this->members->add($member);

        $this->flusher->flush();
    }
}