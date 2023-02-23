<?php
declare(strict_types=1);

namespace App\Controller\Work\Projects\Project\Settings;

use App\Annotation\Guid;
use App\Model\Work\Entity\Projects\Project\Project;
use App\Model\Work\UseCase\Projects\Project\Archive;
use App\Model\Work\UseCase\Projects\Project\Edit;
use App\Model\Work\UseCase\Projects\Project\Reinstate;
use App\Model\Work\UseCase\Projects\Project\Remove;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/work/projects/{project_id}/settings', name: 'work.projects.project.settings')]
#[ParamConverter(data: 'project', options: ['id' => 'project_id'])]
#[IsGranted('ROLE_WORK_MANAGE_PROJECTS')]
class SettingsController extends AbstractController
{

    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    /**
     * @param Project $project
     * @return Response
     */
    #[Route(path: '', name: '', requirements: ['id' => Guid::PATTERN])]
    public function show(Project $project): Response
    {
        return $this->render('app/work/projects/project/settings/show.html.twig', compact('project'));
    }

    /**
     * @param Project $project
     * @param Request $request
     * @param Edit\Handler $handler
     * @return Response
     */
    #[Route(path: '/edit', name: '.edit')]
    public function edit(Project $project, Request $request, Edit\Handler $handler): Response
    {
        $command = Edit\Command::fromProject($project);

        $form = $this->createForm(Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('work.projects.project.show', ['id' => $project->getId()]);
            } catch (\DomainException $e) {
                $this->logger->warning($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/work/projects/project/settings/edit.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Project $project
     * @param Request $request
     * @param Archive\Handler $handler
     * @return Response
     */
    #[Route(path: '/archive', name: '.archive', methods: ['POST'])]
    public function archive(Project $project, Request $request, Archive\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('archive', $request->request->get('token'))) {
            return $this->redirectToRoute('work.projects.project.show', ['id' => $project->getId()]);
        }

        $command = new Archive\Command($project->getId()->getValue());

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->logger->warning($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('work.projects.project.settings', ['project_id' => $project->getId()]);
    }

    /**
     * @param Project $project
     * @param Request $request
     * @param Reinstate\Handler $handler
     * @return Response
     */
    #[Route(path: '/reinstate', name: '.reinstate', methods: ['POST'])]
    public function reinstate(Project $project, Request $request, Reinstate\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('reinstate', $request->request->get('token'))) {
            return $this->redirectToRoute('work.projects.project.settings', ['project_id' => $project->getId()]);
        }

        $command = new Reinstate\Command($project->getId()->getValue());

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->logger->warning($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('work.projects.project.settings', ['project_id' => $project->getId()]);
    }

    /**
     * @param Project $project
     * @param Request $request
     * @param Remove\Handler $handler
     * @return Response
     */
    #[Route(path: '/delete', name: '.delete', methods: ['POST'])]
    public function delete(Project $project, Request $request, Remove\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('work.projects.project.settings', ['project_id' => $project->getId()]);
        }

        $command = new Remove\Command($project->getId()->getValue());

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->logger->warning($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('work.projects');
    }
}