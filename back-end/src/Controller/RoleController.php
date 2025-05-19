<?php

namespace App\Controller;

use App\Entity\Role;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;

#[Route('/api/roles', name: 'api.roles.')]
#[OA\Tag(name: 'Roles')]
final class RoleController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
    ) {
    }

    #[Route(name: 'index', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Retourne la liste des rôles',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Role::class, groups: ['role:show']))
        )
    )]
    public function index(RoleRepository $roleRepository): JsonResponse
    {
        $roles = $roleRepository->findAll();
        return new JsonResponse(
            $this->serializer->serialize($roles, 'json'),
            Response::HTTP_OK,
            [],
            true
        );
    }

    #[Route(name: 'create', methods: ['POST'])]
    #[OA\RequestBody(
        description: 'Données du rôle',
        required: true,
        content: new OA\JsonContent(ref: new Model(type: Role::class, groups: ['role:create']))
    )]
    #[OA\Response(
        response: 201,
        description: 'Rôle créé',
        content: new OA\JsonContent(ref: new Model(type: Role::class, groups: ['role:show']))
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
            $role = $this->serializer->deserialize($request->getContent(), Role::class, 'json');
            $entityManager->persist($role);
            $entityManager->flush();

            return new JsonResponse(
                $this->serializer->serialize($role, 'json'),
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
        description: 'ID du rôle',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string', format: 'ulid', example: '01H2XJWN8D8RJXPTH2FWVG6PKG')
    )]
    #[OA\Response(
        response: 200,
        description: 'Retourne un rôle',
        content: new OA\JsonContent(ref: new Model(type: Role::class, groups: ['role:show']))
    )]
    public function show(Role $role): JsonResponse
    {
        return new JsonResponse(
            $this->serializer->serialize($role, 'json'),
            Response::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    #[OA\Parameter(
        name: 'id',
        description: 'ID du rôle',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string', format: 'ulid', example: '01H2XJWN8D8RJXPTH2FWVG6PKG')
    )]
    #[OA\RequestBody(
        description: 'Données du rôle à mettre à jour',
        required: true,
        content: new OA\JsonContent(ref: new Model(type: Role::class, groups: ['role:edit']))
    )]
    #[OA\Response(
        response: 200,
        description: 'Rôle mis à jour',
        content: new OA\JsonContent(ref: new Model(type: Role::class, groups: ['role:show']))
    )]
    public function update(Request $request, Role $role, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $updatedRole = $this->serializer->deserialize(
                $request->getContent(),
                Role::class,
                'json',
                ['object_to_populate' => $role]
            );
            
            $entityManager->flush();

            return new JsonResponse(
                $this->serializer->serialize($updatedRole, 'json'),
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
        description: 'ID du rôle',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string', format: 'ulid', example: '01H2XJWN8D8RJXPTH2FWVG6PKG')
    )]
    #[OA\Response(
        response: 204,
        description: 'Rôle supprimé'
    )]
    public function delete(Role $role, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $entityManager->remove($role);
            $entityManager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}