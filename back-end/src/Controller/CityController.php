<?php

namespace App\Controller;

use App\Entity\City;
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

#[Route('/api/cities', name: 'api.cities.')]
#[OA\Tag(name: 'Cities')]
final class CityController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
    ) {
    }

    #[Route(name: 'index', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Retourne la liste des villes',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: City::class, groups: ['city:show']))
        )
    )]
    public function index(CityRepository $cityRepository): JsonResponse
    {
        $cities = $cityRepository->findAll();
        return new JsonResponse(
            $this->serializer->serialize([
                    'success' => true,
                    'message' => 'Villes récupérées avec succès',
                    'data' => $cities,
                ]
                , 'json', ['groups' => ['city:show']]),
            Response::HTTP_OK,
            [],
            true
        );
    }

    #[Route(name: 'create', methods: ['POST'])]
    #[OA\RequestBody(
        description: 'Données de la ville',
        required: true,
        content: new OA\JsonContent(ref: new Model(type: City::class, groups: ['city:create']))
    )]
    #[OA\Response(
        response: 201,
        description: 'Ville créée',
        content: new OA\JsonContent(ref: new Model(type: City::class, groups: ['city:show']))
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
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $city = $this->serializer->deserialize($request->getContent(), City::class, 'json');
            $entityManager->persist($city);
            $entityManager->flush();

            return new JsonResponse(
                $this->serializer->serialize([
                        'success' => true,
                        'message' => 'Ville créé avec succès',
                        'data' => $city,
                    ]
                    , 'json', ['groups' => ['city:show', 'road:show']]),
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
        description: 'ID de la ville',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string', format: 'uuid', example: '01H2XJWN8D8RJXPTH2FWVG6PKG')
    )]
    #[OA\Response(
        response: 200,
        description: 'Retourne une ville',
        content: new OA\JsonContent(ref: new Model(type: City::class, groups: ['city:show']))
    )]
    public function show(City $city): JsonResponse
    {
        return new JsonResponse(
            $this->serializer->serialize([
                    'success' => true,
                    'message' => 'Ville récupéré avec succès',
                    'data' => $city
                ]
                , 'json', ['groups' => ['city:show']]),
            Response::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    #[OA\Parameter(
        name: 'id',
        description: 'ID de la ville',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string', format: 'uuid', example: '01H2XJWN8D8RJXPTH2FWVG6PKG')
    )]
    #[OA\RequestBody(
        description: 'Données de la ville à mettre à jour',
        required: true,
        content: new OA\JsonContent(ref: new Model(type: City::class, groups: ['city:edit']))
    )]
    #[OA\Response(
        response: 200,
        description: 'Ville mise à jour',
        content: new OA\JsonContent(ref: new Model(type: City::class, groups: ['city:show']))
    )]
    public function update(Request $request, City $city, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

            if (isset($data['name'])) {
                $city->setName($data['name']);
            }
            if (isset($data['pays'])) {
                $city->setPays($data['pays']);
            }
            
            $entityManager->flush();

            return new JsonResponse(
                $this->serializer->serialize([
                        'success' => true,
                        'message' => 'Ville mis à jour avec succès',
                        'data' => $city
                    ]
                    , 'json', ['groups' => ['city:show']]),
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
        description: 'ID de la ville',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string', format: 'uuid', example: '01H2XJWN8D8RJXPTH2FWVG6PKG')
    )]
    #[OA\Response(
        response: 204,
        description: 'Ville supprimée'
    )]
    public function delete(City $city, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $entityManager->remove($city);
            $entityManager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}