<?php
declare(strict_types=1);

namespace App\Controller\Profile;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: "/profile/email")]
class EmailController extends AbstractController
{
    public function __construct(private LoggerInterface $logger)
    {
    }

}