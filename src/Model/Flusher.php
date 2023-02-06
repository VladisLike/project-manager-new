<?php
declare(strict_types=1);

namespace App\Model;

use Doctrine\ORM\EntityManagerInterface;

class Flusher
{

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function flush(): void
    {
        $this->entityManager->flush();
    }
}