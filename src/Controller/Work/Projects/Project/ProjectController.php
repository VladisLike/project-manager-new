<?php
declare(strict_types=1);

namespace App\Controller\Work\Projects\Project;

use App\Annotation\Guid;
use App\Model\Work\Entity\Projects\Project\Project;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/work/projects/{id}', name: 'work.projects.project')]
class ProjectController extends AbstractController
{
    /**
     * @param Project $project
     * @return Response
     */
    #[Route(path: '', name: '.show', requirements: ['id' => Guid::PATTERN])]
    public function show(Project $project): Response
    {
        return $this->render('app/work/projects/project/show.html.twig', compact('project'));
    }
}