<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Repository\EventRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Entity\Event;
use App\Entity\Category;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use DateTimeImmutable;

class EventController extends AbstractController
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    #[Route('/event', name: 'app_event')]
    public function index(): Response
    {
        return $this->render('event/index.html.twig', [
            'controller_name' => 'EventController',
        ]);
    }

    #[Route('/add_event', name: 'show_add_event')]
    public function showAddEvent(): Response
    {
        return $this->render('event/add_event.html.twig', [
            'controller_name' => 'EventController',
        ]);
    }

    #[Route('/edit_event/{id}', name: 'show_edit_event')]
    public function editEvent(EntityManagerInterface $entityManager, $id): Response
    {
        $event = $entityManager->getRepository(Event::class)->find($id);
        return $this->render('event/edit_event.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/test', name: 'events_list', methods: "GET")]
    public function getEvents(EventRepository $eventRepository): JsonResponse
    {
        $events = $eventRepository->findAll();
        
        // Сериализация данных в JSON
        $data = [];
        foreach ($events as $event) {
            $data[] = [
                'id' => $event->getId(),
                'name' => $event->getName(),
                'content' => $event->getContent(),
                'image' => $event->getImage(),
                'date' => $event->getDate(),
                'category' => $event->getCategory()->getName(),
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/api/events/{id}', name: 'event_detail')]
    public function getEventDetail($id, EventRepository $eventRepository): JsonResponse
    {
        $event = $eventRepository->find($id);

        if (!$event) {
            return new JsonResponse(['error' => 'Event not found'], 404);
        }

        $data = [
            'id' => $event->getId(),
            'name' => $event->getName(),
            'content' => $event->getContent(),
            'image' => $event->getImage(),
            'date' => $event->getDate(),
            'category' => $event->getCategory()->getCategory(),
        ];

        return new JsonResponse($data);
    }

    #[Route('/test', name: 'add_event', methods: ["POST"])]
    public function addEvent(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer) {
        try {
            // $requestData = $request->request->all();
            // $jsonData = $request->getContent();
            // $requestData = $serializer->deserialize($jsonData, 'App\Entity\Event', 'json');
            $requestData = json_encode($request->getContent());
            $requestData = json_decode($request->getContent(), true);

            // dump(json_encode($request->getContent()));
            dump($requestData);
    
            if (!$requestData || !isset($requestData['name'])) {
                throw new \Exception('Invalid data');
            }

            $category = $entityManager->getRepository(Category::class)->findOneBy(['name' => $requestData['category']]);
            dump($category);
            $category_id = null;
            if ($category == null) {
                dump($requestData['category']);
                // Если категория не существует, создаем новую запись
                $newCategory = new Category();
                $newCategory->setName($requestData['category']);
                dump($newCategory);
                $entityManager->persist($newCategory);
                $entityManager->flush();
                $category = $newCategory;
                dump($category);
                $category_id = $newCategory->getId();
            } 
            else {
                // Если категория уже существует, используем ее ID
                $category_id = $category->getId();
            }
    
            $event = new Event();
            $event->setName($requestData['name']);
            $event->setContent($requestData['content']);
            $event->setImage($requestData['image']);
            dump(gettype($requestData['date']));
            // $event->setDate($requestData['date']);
            $event->setDate(new DateTimeImmutable($requestData['date']));
            // $test = 2;
            // dump($event->getDate());
            $event->setCategory($category);
    
            $entityManager->persist($event);
            $entityManager->flush();
    
            $data = [
                'status' => 201,
                'success' => "Событие успешно добавлено",
            ];
            return $this->json($data, 200, [], ['Content-Type' => 'application/ld+json']);
        } 
        catch (\Exception $e) {
            $data = [
                'status' => 422,
                'errors' => "Данные не валидны",
            ];
            return $this->json($data, 422, [], ['Content-Type' => 'application/ld+json']);
        }
    }

    #[Route('/test/{id}', name: 'delete_event', methods: ["DELETE"])]
    public function deleteEvent($id, EventRepository $eventRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $event = $eventRepository->find($id);

        if (!$event) {
            $data = [
                'status' => 404,
                'errors' => "Новость не найдена",
            ];
            return $this->json($data, 404);
        }
        
        $entityManager->remove($event);
        $entityManager->flush();
        $data = [
            'status' => 200,
            'errors' => "Новость удалена успешно",
        ];
        return $this->json($data);
    }

    #[Route('/test/{id}', name: 'update_event', methods: ["PUT"])]
    public function updateEvent(Request $request, EntityManagerInterface $entityManager, EventRepository $eventRepository, $id){
        try{
            $event = $eventRepository->find($id);

            $requestData = json_encode($request->getContent());
            $requestData = json_decode($request->getContent(), true);

            dump($event);
            dump($requestData['category']);
            if (!$event){
                $data = [
                    'status' => 404,
                    'errors' => "Новость не найдена",
                ];
                return $this->json($data, 404);
            }
            // if (!$request || !$request->get('name')) {
            //     throw new \Exception();
            // }

            // находим нужный id категории по ее названию
            $category = $entityManager->getRepository(Category::class)->findOneBy(['name' => $requestData['category']]);
        
            $event->setName($requestData['name']);
            $event->setContent($requestData['content']);
            $event->setImage($requestData['image']);
            $event->setDate(new DateTimeImmutable($requestData['date']));
            $event->setCategory($category);
            $entityManager->flush();
        
            $data = [
                'status' => 200,
                'errors' => "Новость успешно отредактирована",
            ];
            return $this->json($data);
        
        }
        catch (\Exception $e){
            $data = [
                'status' => 422,
                'errors' => "Данные не валидны",
            ];
            return $this->json($data, 422);
        }
    }
}
