<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Attribute\Security;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


#[Route('/api', name: 'api.auth.')]
#[OA\Tag(name: 'Authentification')]
class AuthController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    #[Route('/login', name: 'login', methods: ['POST'])]
    #[OA\Post(
        path: '/api/login',
        description: 'Connecte un utilisateur et retourne son token JWT',
        summary: 'Connexion utilisateur'
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: new Model(type: User::class, groups: ['user:login']))
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Authentification réussie',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'token', type: 'string', example: 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...'),
            ]
        )
    )]
    #[OA\Response(
        response: Response::HTTP_UNAUTHORIZED,
        description: 'Identifiants invalides',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'code', type: 'integer', example: 401),
                new OA\Property(property: 'message', type: 'string', example: 'Invalid credentials.')
            ]
        )
    )]
    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        // Simulez ici les étapes nécessaires pour la connexion :
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        // Validation basique des champs
        if (!$email || !$password) {
            return $this->json([
                'success' => false,
                'message' => 'Les identifiants sont requis'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Authentification fictive - à remplacer par la logique réelle
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user || !$this->passwordHasher->isPasswordValid($user, $password)) {
            return $this->json([
                'success' => false,
                'message' => 'Identifiants invalides'
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Génération du token JWT - remplacez par votre logique réelle de JWT
        $token = 'abc.def.ghi';

        // Retour de la réponse structurée
        return $this->json([
            'success' => true,
            'message' => 'Connexion réussie',
            'data' => [
                'token' => $token
            ]
        ], Response::HTTP_OK);
    }


    #[Route('/me', name: 'me', methods: ['GET'])]
    #[Security(name: 'Bearer')]
    #[OA\Get(
        path: '/api/me',
        description: 'Récupère les informations de l\'utilisateur connecté',
        summary: 'Profil utilisateur courant'
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Retourne les informations de l\'utilisateur',
        content: new Model(type: User::class, groups: ['user:show'])
    )]
    #[OA\Response(
        response: Response::HTTP_UNAUTHORIZED,
        description: 'Non authentifié',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'JWT Token non valide')
            ]
        )
    )]
    public function me(#[CurrentUser] User $user): JsonResponse
    {
        return $this->json($user, Response::HTTP_OK, [], ['groups' => ['user:show', 'role:show', 'booking:show']]);
    }

    #[Route('/register', name: 'register', methods: ['POST'])]
    #[OA\Post(
        path: '/api/register',
        description: 'Crée un nouveau compte utilisateur',
        summary: 'Inscription d\'un nouvel utilisateur'
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: new Model(type: User::class, groups: ['user:create']))
    )]
    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'Utilisateur créé avec succès',
        content: new Model(type: User::class, groups: ['user:show'])
    )]
    #[OA\Response(
        response: Response::HTTP_BAD_REQUEST,
        description: 'Données invalides',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'errors',
                    type: 'object',
                    example: [
                        'email' => ['Cet email est déjà utilisé.'],
                        'password' => ['Le mot de passe doit contenir au moins 6 caractères.'],
                        'role' => ['Le rôle spécifié n\'existe pas.']
                    ]
                )
            ]
        )
    )]
    public function register(Request $request, RoleRepository $roleRepository): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            // Vérif email
            $existingUser = $this->entityManager
                ->getRepository(User::class)
                ->findOneBy(['email' => $data['email'] ?? null]);

            if ($existingUser) {
                return $this->json([
                    'errors' => [
                        'email' => ['Cet email est déjà utilisé.']
                    ]
                ], Response::HTTP_BAD_REQUEST);
            }

            // Vérif rôle
            $role = $data['role'] ?? null;
            $role = $role ? $roleRepository->findOneBy(['name'=> $role]) : $roleRepository->findOneBy(['name'=> 'USER']);;

            if (!$role) {
                return $this->json([
                    'errors' => [
                        'role' => ['Le rôle spécifié n\'existe pas.']
                    ]
                ], Response::HTTP_BAD_REQUEST);
            }

            // Retirer roleId avant désérialisation
            unset($data['roleId']);

            /** @var User $user */
            $user = $this->serializer->deserialize(
                json_encode($data),
                User::class,
                'json',
                ['groups' => ['user:create']]
            );

            // Affecter le rôle
            $user->setRole($role);

            // Validation
            $errors = $this->validator->validate($user);
            if (count($errors) > 0) {
                $errorsArray = [];
                foreach ($errors as $error) {
                    $errorsArray[$error->getPropertyPath()][] = $error->getMessage();
                }
                return $this->json(['errors' => $errorsArray], Response::HTTP_BAD_REQUEST);
            }

            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            );
            $user->setPassword($hashedPassword);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->json(
                [
                    'success' => true,
                    'message' => 'Account create successful',
                    'data' => $this->serializer->serialize($user, 'json', ['groups' => ['user:show']])
                ],
                Response::HTTP_CREATED,
                [],
                ['groups' => ['user:show']]
            );

        } catch (\Exception $e) {
            return $this->json([
                'errors' => [
                    'global' => ['Une erreur est survenue lors de l\'inscription.']
                ],
                'debug' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/logout', name: 'logout', methods: ['POST'])]
    #[Security(name: 'Bearer')]
    #[OA\Post(
        path: '/api/logout',
        description: 'Déconnecte l\'utilisateur courant',
        summary: 'Déconnexion utilisateur'
    )]
    #[OA\Response(
        response: Response::HTTP_NO_CONTENT,
        description: 'Déconnexion réussie'
    )]
    public function logout(): void
    {
    }
}