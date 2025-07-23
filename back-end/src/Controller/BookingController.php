<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Repository\BookingRepository;
use App\Repository\SessionRepository;
use App\Repository\UserRepository;
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

#[Route('/api/bookings', name: 'api.booking.')]
#[OA\Tag(name: 'Booking')]
class BookingController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator
    ) {}

    #[Route(name: 'index', methods: ['GET'])]
    #[OA\Get(
        description: 'Retourne toutes les réservations',
        summary: 'Liste des réservations'
    )]
    #[OA\Response(
        response: 200,
        description: 'Liste des réservations',
        content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: new Model(type: Booking::class, groups: ['booking:show', 'booking-user:show', 'booking-session:show'])))
    )]
    public function index(BookingRepository $bookingRepository): JsonResponse
    {
        $bookings = $bookingRepository->findAll();

        return new JsonResponse(
            $this->serializer->serialize([
                'success' => true,
                'message' => 'Réservations récupérées avec succès',
                'data' => $bookings,
            ], 'json', ['groups' => ['booking:show', 'user:show', 'session:show']]),
            Response::HTTP_OK,
            [],
            true
        );
    }

    #[Route(name: 'create', methods: ['POST'])]
    #[OA\Post(
        description: 'Créer une nouvelle réservation',
        summary: 'Créer une réservation'
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: new Model(type: Booking::class, groups: ['booking:create']))
    )]
    #[OA\Response(
        response: 201,
        description: 'Réservation créée',
        content: new OA\JsonContent(ref: new Model(type: Booking::class, groups: ['booking:show', 'booking-user:show', 'booking-session:show']))
    )]
    #[OA\Response(
        response: 400,
        description: 'Erreur de validation',
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'error', type: 'string')
        ])
    )]
    #[Route(name: 'create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $entityManager,
        SessionRepository $sessionRepository,
        UserRepository $userRepository
    ): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);

            // Récupération de l'utilisateur connecté
            $user = $userRepository->findOneBy(['id' => $this->getUser()->getId()]);

            if (!$user) {
                return new JsonResponse(['error' => 'Utilisateur non trouvé.'], Response::HTTP_UNAUTHORIZED);
            }

            // Récupération de la session réservée
            $session = $sessionRepository->find($data['session_id']);

            if (!$session) {
                return new JsonResponse(['error' => 'Session non trouvée.'], Response::HTTP_BAD_REQUEST);
            }

            // Création de la réservation
            $booking = new Booking();
            $booking->setUser($user);
            $booking->setSession($session);

            // Gestion de la date de réservation
            if (isset($data['reservation_date'])) {
                $reservationDate = new \DateTimeImmutable($data['reservation_date']);
                $booking->setReservationDate($reservationDate);
            } else {
                $booking->setReservationDate(new \DateTimeImmutable());
            }

            // Calcul du prix ou ajout d'autres champs si nécessaire
            $booking->setPrice($data['price'] ?? $session->getUnitPrice());

            // Persist et flush
            $entityManager->persist($booking);
            $entityManager->flush();

            return new JsonResponse(
                $this->serializer->serialize([
                    'success' => true,
                    'message' => 'Réservation créée avec succès',
                    'data' => $booking,
                ], 'json', ['groups' => ['booking:show', 'booking-user:show', 'booking-session:show']]),
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
        description: 'Récupère une réservation par son identifiant',
        summary: 'Afficher une réservation'
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string', format: 'ulid', example: '01H2XJWN8D8RJXPTH2FWVG6PKG')
    )]
    #[OA\Response(
        response: 200,
        description: 'Réservation trouvée',
        content: new OA\JsonContent(ref: new Model(type: Booking::class, groups: ['booking:show', 'booking-user:show', 'booking-session:show']))
    )]
    #[OA\Response(response: 404, description: 'Réservation non trouvée')]
    public function show(Booking $booking): JsonResponse
    {
        return new JsonResponse(
            $this->serializer->serialize([
                'success' => true,
                'message' => 'Réservation récupérée avec succès',
                'data' => $booking,
            ], 'json', ['groups' => ['booking:show', 'booking-user:show', 'booking-session:show']]),
            Response::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    #[OA\Put(
        description: 'Met à jour une réservation existante',
        summary: 'Modifier une réservation'
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: new Model(type: Booking::class, groups: ['booking:create']))
    )]
    #[OA\Response(
        response: 200,
        description: 'Réservation mise à jour',
        content: new OA\JsonContent(ref: new Model(type: Booking::class, groups: ['booking:show', 'booking-user:show', 'booking-session:show']))
    )]
    public function update(Request $request, Booking $booking, EntityManagerInterface $em): JsonResponse
    {
        try {
            $this->serializer->deserialize(
                $request->getContent(),
                Booking::class,
                'json',
                ['object_to_populate' => $booking, 'groups' => ['booking:create']]
            );

            $errors = $this->validator->validate($booking);
            if (count($errors) > 0) {
                $validationErrors = [];
                foreach ($errors as $error) {
                    $validationErrors[$error->getPropertyPath()][] = $error->getMessage();
                }
                return $this->json(['errors' => $validationErrors], Response::HTTP_BAD_REQUEST);
            }

            $em->flush();

            return new JsonResponse(
                $this->serializer->serialize([
                    'success' => true,
                    'message' => 'Réservation mise à jour avec succès',
                    'data' => $booking,
                ], 'json', ['groups' => ['booking:show', 'booking-user:show', 'booking-session:show']]),
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
        description: 'Supprime une réservation existante',
        summary: 'Supprimer une réservation'
    )]
    #[OA\Response(response: 204, description: 'Réservation supprimée')]
    #[OA\Response(response: 400, description: 'Erreur lors de la suppression')]
    public function delete(Booking $booking, EntityManagerInterface $em): JsonResponse
    {
        try {
            $em->remove($booking);
            $em->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
