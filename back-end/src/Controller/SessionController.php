<?php

namespace App\Controller;

use App\Entity\Session;
use App\Repository\SessionRepository;
use App\Repository\BusRepository;
use App\Repository\CityRepository;
use App\Repository\RoadRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;

#[Route('/api/sessions', name: 'api.sessions.')]
#[OA\Tag(name: 'Sessions')]
final class SessionController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
    ) {
    }

    #[Route(name: 'index', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Retourne la liste des sessions',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Session::class, groups: ['session:show', 'session-city:show', 'road-city:show', 'bus:show']))
        )
    )]
    public function index(SessionRepository $sessionRepository, SerializerInterface $serializer): JsonResponse
    {
        $sessions = $sessionRepository->findAll();
        $json = $serializer->serialize($sessions, 'json', ['groups' => ['session:show', 'session-city:show', 'road-city:show', 'bus:show']]);

        return new JsonResponse($json, JsonResponse::HTTP_OK, [], true);
    }

    #[Route(name: 'create', methods: ['POST'])]
    #[OA\RequestBody(
        description: 'Données de la session',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(
                type: Session::class,
                groups: ['session:create'],
                options: ['groups' => ['session:create']]
            )
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Session créée',
        content: new OA\JsonContent(ref: new Model(type: Session::class, groups: ['session:show', 'session-city:show', 'road-city:show', 'bus:show']))
    )]
    public function create(
        Request $request,
        EntityManagerInterface $entityManager,
        RoadRepository $roadRepository,
        BusRepository $busRepository,
        CityRepository $cityRepository
    ): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);

            // Récupération des entités liées
            $road = $roadRepository->find($data['roadId']);
            $bus = $busRepository->find($data['busId']);
            $startCity = $cityRepository->find($data['start_city_id']);
            $arrivedCity = $cityRepository->find($data['arrived_city_id']);

            if (!$road || !$bus ) {
                return new JsonResponse(['error' => 'Entités liées non trouvées'], Response::HTTP_BAD_REQUEST);
            }

            // Suppression des IDs des entités liées avant la désérialisation
            unset($data['road'], $data['bus']);
            $session = $this->serializer->denormalize($data, Session::class, 'json');

            // Attribution des entités liées
            $session->setRoad($road);
            $session->setBus($bus);
            $session->setStartCity($startCity);
            $session->setArrivedCity($arrivedCity);

            $entityManager->persist($session);
            $entityManager->flush();

            return new JsonResponse(
                $this->serializer->serialize($session, 'json', ['groups' => ['session:show', 'session-city:show', 'road-city:show', 'bus:show']]),
                Response::HTTP_CREATED,
                [],
                true
            );
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[OA\Parameter(
        name: 'id',
        description: 'ID de la session',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Response(
        response: 200,
        description: 'Retourne une session',
        content: new OA\JsonContent(ref: new Model(type: Session::class, groups: ['session:show']))
    )]
    public function show(Session $session): JsonResponse
    {
        return new JsonResponse(
            $this->serializer->serialize($session, 'json', ['groups' => ['session:show', 'session-city:show', 'road-city:show', 'bus:show']]),
            Response::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    #[OA\Parameter(
        name: 'id',
        description: 'ID de la session',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\RequestBody(
        description: 'Données de la session à mettre à jour',
        required: true,
        content: new OA\JsonContent(ref: new Model(type: Session::class, groups: ['session:edit']))
    )]
    public function update(
        Request $request,
        Session $session,
        EntityManagerInterface $entityManager,
        RoadRepository $roadRepository,
        BusRepository $busRepository,
        CityRepository $cityRepository
    ): JsonResponse {
        try {
            // Récupérez les données de la requête JSON
            $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

            // Mettre à jour les propriétés de l'entité existante
            if (isset($data['roadId'])) {
                $road = $roadRepository->find($data['roadId']);
                if (!$road) {
                    return new JsonResponse(['error' => 'Route non trouvée'], Response::HTTP_BAD_REQUEST);
                }
                $session->setRoad($road);
            }

            if (isset($data['busId'])) {
                $bus = $busRepository->find($data['busId']);
                if (!$bus) {
                    return new JsonResponse(['error' => 'Bus non trouvé'], Response::HTTP_BAD_REQUEST);
                }
                $session->setBus($bus);
            }

            if (isset($data['start_city_id'])) {
                $startCity = $cityRepository->find($data['start_city_id']);
                if (!$startCity) {
                    return new JsonResponse(['error' => 'Ville de départ non trouvée'], Response::HTTP_BAD_REQUEST);
                }
                $session->setStartCity($startCity);
            }

            if (isset($data['arrived_city_id'])) {
                $arrivedCity = $cityRepository->find($data['arrived_city_id']);
                if (!$arrivedCity) {
                    return new JsonResponse(['error' => 'Ville d\'arrivée non trouvée'], Response::HTTP_BAD_REQUEST);
                }
                $session->setArrivedCity($arrivedCity);
            }

            if (isset($data['unit_price'])) {
                $session->setUnitPrice((float)$data['unit_price']);
            }

            if (isset($data['departure_date'])) {
                $session->setDepartureDate(new \DateTime($data['departure_date']));
            }

            if (isset($data['estimated_time'])) {
                $session->setEstimatedTime(new \DateTime($data['estimated_time']));
            }

            $entityManager->flush();

            return new JsonResponse(
                $this->serializer->serialize($session, 'json', ['groups' => ['session:show', 'session-city:show', 'road-city:show', 'bus:show']]),
                Response::HTTP_OK,
                [],
                true
            );
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[OA\Parameter(
        name: 'id',
        description: 'ID de la session',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string', format: 'uuid')
    )]
    #[OA\Response(
        response: 204,
        description: 'Session supprimée'
    )]
    public function delete(Session $session, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $entityManager->remove($session);
            $entityManager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}