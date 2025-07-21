<?php

namespace App\Controller;

use App\Entity\Bus;
use App\Repository\BusRepository;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/buses', name: 'api.bus.')]
#[OA\Tag(name: 'Bus')]
class BusController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator
    ) {}

    #[Route(name: 'index', methods: ['GET'])]
    #[OA\Get(
        description: 'Retourne l’ensemble des bus enregistrés',
        summary: 'Liste des bus'
    )]
    #[OA\Response(
        response: 200,
        description: 'Liste des bus',
        content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: new Model(type: Bus::class, groups: ['bus:show'])))
    )]
    public function index(BusRepository $busRepository): JsonResponse
    {
        $buses = $busRepository->findAll();

        return new JsonResponse(
            $this->serializer->serialize([
                    'success' => true,
                    'message' => 'Bus récupérés avec succès',
                    'data' => $buses,
                ]
                , 'json', ['groups' => ['bus:show']]),
            Response::HTTP_OK,
            [],
            true
        );
    }

    #[Route(name: 'create', methods: ['POST'])]
    #[OA\Post(
        description: 'Ajoute un nouveau bus à la flotte',
        summary: 'Créer un nouveau bus'
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: new Model(type: Bus::class, groups: ['bus:create']))
    )]
    #[OA\Response(
        response: 201,
        description: 'Bus créé',
        content: new OA\JsonContent(ref: new Model(type: Bus::class, groups: ['bus:show']))
    )]
    #[OA\Response(
        response: 400,
        description: 'Erreur de validation',
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'error', type: 'string')
        ])
    )]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            /** @var Bus $bus */
            $bus = $this->serializer->deserialize($request->getContent(), Bus::class, 'json', ['groups' => ['bus:create']]);

            $errors = $this->validator->validate($bus);
            if (count($errors) > 0) {
                $errorsArray = [];
                foreach ($errors as $error) {
                    $errorsArray[$error->getPropertyPath()][] = $error->getMessage();
                }
                return $this->json(['errors' => $errorsArray], Response::HTTP_BAD_REQUEST);
            }

            $em->persist($bus);
            $em->flush();

            return new JsonResponse(
                $this->serializer->serialize([
                    'success' => true,
                    'message' => 'Bus créé avec succès',
                    'data' => $bus,
                ], 'json', ['groups' => ['bus:show']]),
                Response::HTTP_CREATED,
                [],
                true
            );
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[OA\Get(
        description: 'Récupère les détails d’un bus par son identifiant',
        summary: 'Afficher un bus'
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Response(
        response: 200,
        description: 'Bus trouvé',
        content: new OA\JsonContent(ref: new Model(type: Bus::class, groups: ['bus:show']))
    )]
    #[OA\Response(response: 404, description: 'Bus non trouvé')]
    public function show(Bus $bus): JsonResponse
    {
        return new JsonResponse(
            $this->serializer->serialize([
                'success' => true,
                'message' => 'Bus récupré avec  succès',
                'data' => $bus,
            ], 'json', ['groups' => ['bus:show']]),
            Response::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    #[OA\Put(
        description: 'Met à jour les informations d’un bus existant',
        summary: 'Modifier un bus'
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: new Model(type: Bus::class, groups: ['bus:create']))
    )]
    #[OA\Response(
        response: 200,
        description: 'Bus mis à jour',
        content: new OA\JsonContent(ref: new Model(type: Bus::class, groups: ['bus:show']))
    )]
    public function update(Request $request, Bus $bus, EntityManagerInterface $em): JsonResponse
    {
        try {
            $this->serializer->deserialize(
                $request->getContent(),
                Bus::class,
                'json',
                ['object_to_populate' => $bus, 'groups' => ['bus:create']]
            );

            $errors = $this->validator->validate($bus);
            if (count($errors) > 0) {
                $errorsArray = [];
                foreach ($errors as $error) {
                    $errorsArray[$error->getPropertyPath()][] = $error->getMessage();
                }
                return $this->json(['errors' => $errorsArray], Response::HTTP_BAD_REQUEST);
            }

            $em->flush();

            return new JsonResponse(
                $this->serializer->serialize([
                    'success' => true,
                    'message' => 'Bus mis à jour avec succès',
                    'data' => $bus,
                ], 'json', ['groups' => ['bus:show']]),
                Response::HTTP_OK,
                [],
                true
            );
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[OA\Delete(
        description: 'Supprime un bus existant de la base de données',
        summary: 'Supprimer un bus'
    )]
    #[OA\Response(response: 204, description: 'Bus supprimé')]
    #[OA\Response(response: 400, description: 'Erreur lors de la suppression')]
    public function delete(Bus $bus, EntityManagerInterface $em): JsonResponse
    {
        try {
            $em->remove($bus);
            $em->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
