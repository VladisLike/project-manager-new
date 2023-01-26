<?php
declare(strict_types=1);

namespace App\ZoomIntegration\Controller;

use App\ZoomIntegration\Service\ZoomApiServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ZoomController extends AbstractController
{
    private ZoomApiServiceInterface $zoomApiService;

    /**
     * @param ZoomApiServiceInterface $zoomApiService
     */
    public function __construct(ZoomApiServiceInterface $zoomApiService)
    {
        $this->zoomApiService = $zoomApiService;
    }

    #[Route('/zoom', name: 'zoom')]
    public function index(): Response
    {
        $data = [];
        $data['topic'] = 'Zoom Meeting';
        $data['start_date'] = date("Y-m-d h:i:s", strtotime('tomorrow'));
        $data['duration'] = 30;
        $data['type'] = 2;
        $data['password'] = "123456";

        $response = $this->zoomApiService->createMeeting($data);

        return $this->render('zoom/zoom.html.twig', ['data' => $response]);
    }

}