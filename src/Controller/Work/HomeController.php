<?php
declare(strict_types=1);

namespace App\Controller\Work;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @return Response
     */
    #[Route(path: '/work', name: 'work')]
    public function index(): Response
    {
        return $this->redirectToRoute('work.projects');
    }
}