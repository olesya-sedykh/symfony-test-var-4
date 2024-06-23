<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Repository\EventViewRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Entity\EventView;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Serializer\SerializerInterface;

use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\String\Slugger\SluggerInterface;

class EventViewController extends AbstractController
{
    #[Route('/event/view', name: 'app_event_view')]
    public function index(): Response
    {
        return $this->render('event_view/index.html.twig', [
            'controller_name' => 'EventViewController',
        ]);
    }

    #[Route('/api/event_views', name: 'event_views_list', methods: "GET")]
    public function getEventViews(EventViewRepository $eventViewRepository): JsonResponse
    {
        $events = $eventViewRepository->findAll();
        
        // Сериализация данных в JSON
        $data = [];
        foreach ($events as $event) {
            $data[] = [
                'id' => $event->getId(),
                'name' => $event->getName(),
                'content' => $event->getContent(),
                'image' => $event->getImage(),
                'date' => $event->getDate(),
                'category' => $event->getCategory(),
            ];
        }

        return new JsonResponse($data);
    }
}
