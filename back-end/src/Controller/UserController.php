<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;

#[Route('/api/users', name: 'api.users.')]
#[OA\Tag(name: 'Users')]
final class UserController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
    ) {
    }

    #[Route(name: 'index', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Retourne la liste des utilisateurs',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: User::class, groups: ['user:show', 'role:show', 'booking:show']))
        )
    )]
    public function index(UserRepository $userRepository, SerializerInterface $serializer): JsonResponse
    {
        $users = $userRepository->findAll();

        $json = $serializer->serialize($users, 'json', ['groups' => ['user:show', 'role:show', 'booking:show']]);

        return new JsonResponse($json, JsonResponse::HTTP_OK, [], true);
    }

    #[Route(name: 'create', methods: ['POST'])]
    #[OA\RequestBody(
        description: 'Données de l\'utilisateur',
        required: true,
        content: new OA\JsonContent(ref: new Model(type: User::class, groups: ['user:create']))
    )]
    #[OA\Response(
        response: 201,
        description: 'Utilisateur créé',
        content: new OA\JsonContent(ref: new Model(type: User::class, groups: ['user:show']))
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
    public function create(Request $request, EntityManagerInterface $entityManager, RoleRepository $roleRepository): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            // Récupère le rôle depuis l'ID transmis
            $role = $roleRepository->find($data['role']);
            if (!$role) {
                return new JsonResponse(['error' => 'Rôle non trouvé'], Response::HTTP_BAD_REQUEST);
            }

            // Crée une instance de User sans désérialiser automatiquement `role`
            unset($data['role']);
            $user = $this->serializer->denormalize($data, User::class, 'json');

            $user->setRole($role);

            $entityManager->persist($user);
            $entityManager->flush();

            return new JsonResponse(
                $this->serializer->serialize($user, 'json', ['groups' => ['user:show']]),
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
        description: 'ID de l\'utilisateur',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string', format: 'uuid', example: '01H2XJWN8D8RJXPTH2FWVG6PKG')
    )]
    #[OA\Response(
        response: 200,
        description: 'Retourne un utilisateur',
        content: new OA\JsonContent(ref: new Model(type: User::class, groups: ['user:show']))
    )]
    public function show(User $user): JsonResponse
    {
        return new JsonResponse(
            $this->serializer->serialize($user, 'json'),
            Response::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    #[OA\Parameter(
        name: 'id',
        description: 'ID de l\'utilisateur',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string', format: 'uuid', example: '01H2XJWN8D8RJXPTH2FWVG6PKG')
    )]
    #[OA\RequestBody(
        description: 'Données de l\'utilisateur à mettre à jour',
        required: true,
        content: new OA\JsonContent(ref: new Model(type: User::class, groups: ['user:edit']))
    )]
    #[OA\Response(
        response: 200,
        description: 'Utilisateur mis à jour',
        content: new OA\JsonContent(ref: new Model(type: User::class, groups: ['user:show', 'role:show', 'booking:show']))
    )]
    public function update(Request $request, User $user, EntityManagerInterface $entityManager, RoleRepository $roleRepository): JsonResponse
    {
        dump($user);
        try {
            $data = json_decode($request->getContent(), true);
            if (!$data) {
                return new JsonResponse(['error' => 'Données JSON invalides'], Response::HTTP_BAD_REQUEST);
            }

            // Mise à jour conditionnelle des champs
            if (isset($data['firstName'])) {
                $user->setFirstName($data['firstName']);
            }
            if (isset($data['lastName'])) {
                $user->setLastName($data['lastName']);
            }
            if (isset($data['email'])) {
                $user->setEmail($data['email']);
            }
            if (isset($data['password'])) {
                $user->setPassword($data['password']);
            }
            if (isset($data['role'])) {
                $role = $roleRepository->find($data['role']);
                if (!$role) {
                    return new JsonResponse(['error' => 'Rôle non trouvé'], Response::HTTP_BAD_REQUEST);
                }
                $user->setRole($role);
            }

            $entityManager->flush();

            return new JsonResponse(
                $this->serializer->serialize($user, 'json', ['groups' => ['user:show', 'role:show']]),
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
        description: 'ID de l\'utilisateur',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string', format: 'uuid', example: '01H2XJWN8D8RJXPTH2FWVG6PKG')
    )]
    #[OA\Response(
        response: 204,
        description: 'Utilisateur supprimé'
    )]
    public function delete(User $user, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $entityManager->remove($user);
            $entityManager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}