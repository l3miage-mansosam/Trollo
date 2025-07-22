<?php

namespace App\Controller;

use App\Entity\Road;
use App\Repository\RoadRepository;
use App\Repository\CityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;

#[Route('/api/roads', name: 'api.roads.')]
#[OA\Tag(name: 'Roads')]
final class RoadController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
    ) {
    }

    #[Route(name: 'index', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Retourne la liste des trajets',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Road::class, groups: ['road:show', 'city:show', 'session:show']))
        )
    )]
    public function index(RoadRepository $roadRepository): JsonResponse
    {
        $roads = $roadRepository->findAll();
        return new JsonResponse(
            $this->serializer->serialize($roads, 'json', ['groups' => ['road:show', 'city:show']]),
            Response::HTTP_OK,
            [],
            true
        );
    }

    #[Route(name: 'create', methods: ['POST'])]
    #[OA\RequestBody(
        description: 'Données du trajet',
        required: true,
        content: new OA\JsonContent(ref: new Model(type: Road::class, groups: ['road:create']))
    )]
    #[OA\Response(
        response: 201,
        description: 'Trajet créé',
        content: new OA\JsonContent(ref: new Model(type: Road::class, groups: ['road:show']))
    )]
    #[OA\Response(
        response: 400,
        description: 'Données invalides',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'error', type: 'string')
            ],
            type: 'object'
        )
    )]
    public function create(Request $request, EntityManagerInterface $entityManager, CityRepository $cityRepository): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

            // Récupération des villes
            $startCity = $cityRepository->find($data['startCity']);
            $arrivedCity = $cityRepository->find($data['arrivedCity']);

            if (!$startCity || !$arrivedCity) {
                return new JsonResponse(['error' => 'Ville(s) non trouvée(s)'], Response::HTTP_BAD_REQUEST);
            }

            $road = new Road();
            $road->setStartCity($startCity);
            $road->setArrivedCity($arrivedCity);
            if (isset($data['estimatedTime'])) {
                // Crée un objet DateTime avec uniquement l'heure
                $time = \DateTime::createFromFormat('H:i:s', $data['estimatedTime']);
                if ($time === false) {
                    return new JsonResponse(['error' => 'Format d\'heure invalide. Utilisez le format HH:mm:ss'], Response::HTTP_BAD_REQUEST);
                }
                $road->setEstimatedTime($time);
            }

            $entityManager->persist($road);
            $entityManager->flush();

            return new JsonResponse(
                $this->serializer->serialize($road, 'json', ['groups' => ['road:show']]),
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
        description: 'ID du trajet',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string', format: 'uuid', example: '01H2XJWN8D8RJXPTH2FWVG6PKG')
    )]
    #[OA\Response(
        response: 200,
        description: 'Retourne un trajet',
        content: new OA\JsonContent(ref: new Model(type: Road::class, groups: ['road:show']))
    )]
    public function show(Road $road): JsonResponse
    {
        return new JsonResponse(
            $this->serializer->serialize($road, 'json'),
            Response::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    #[OA\Parameter(
        name: 'id',
        description: 'ID du trajet',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string', format: 'uuid', example: '01H2XJWN8D8RJXPTH2FWVG6PKG')
    )]
    #[OA\RequestBody(
        description: 'Données du trajet à mettre à jour',
        required: true,
        content: new OA\JsonContent(ref: new Model(type: Road::class, groups: ['road:edit']))
    )]
    #[OA\Response(
        response: 200,
        description: 'Trajet mis à jour',
        content: new OA\JsonContent(ref: new Model(type: Road::class, groups: ['road:show']))
    )]
    public function update(Request $request, Road $road, EntityManagerInterface $entityManager, CityRepository $cityRepository): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

            if (isset($data['startCity'])) {
                $startCity = $cityRepository->find($data['startCity']);
                if (!$startCity) {
                    return new JsonResponse(['error' => 'Ville de départ non trouvée'], Response::HTTP_BAD_REQUEST);
                }
                $road->setStartCity($startCity);
            }

            if (isset($data['arrived_city'])) {
                $arrivedCity = $cityRepository->find($data['arrived_city']);
                if (!$arrivedCity) {
                    return new JsonResponse(['error' => 'Ville d\'arrivée non trouvée'], Response::HTTP_BAD_REQUEST);
                }
                $road->setArrivedCity($arrivedCity);
            }

            if (isset($data['estimatedTime'])) {
                $road->setEstimatedTime(new \DateTime($data['estimatedTime']));
            }

            $entityManager->flush();

            return new JsonResponse(
                $this->serializer->serialize($road, 'json', ['groups' => ['road:show']]),
                Response::HTTP_OK,
                [],
                true
            );
        } catch (\JsonException $e) {
            return new JsonResponse(['error' => 'Format JSON invalide : ' . $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[OA\Parameter(
        name: 'id',
        description: 'ID du trajet',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string', format: 'uuid', example: '01H2XJWN8D8RJXPTH2FWVG6PKG')
    )]
    #[OA\Response(
        response: 204,
        description: 'Trajet supprimé'
    )]
    public function delete(Road $road, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $entityManager->remove($road);
            $entityManager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}