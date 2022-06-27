<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Repository\TicketRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\PessimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Ticket\Request\RequestValidator;

/**
 * Class TicketController
 * @package App\Controller
 *
 * @Route("/api", name="api_")
 */
class TicketController extends AbstractController
{
    /**
     *
     * Action to list all the existing tickets.
     *
     * @param ManagerRegistry $connection
     * @return JsonResponse
     *
     * @Route("/ticket", name="tickets", methods={"GET"})
     */
    public function index(ManagerRegistry $connection): JsonResponse
    {
        try {
            /**
             * @var array $tickets
             *   Array with all the retrieved ticket objects.
             */
            $tickets = $connection->getRepository(Ticket::class)->findAll();

            /**
             * @var Ticket $ticket
             *   Mapped ticket object.
             */
            $data = array_map(function(Ticket $ticket) {
                return [
                    'id' => $ticket->getId(),
                    'description' => $ticket->getDescription(),
                    'date' => $ticket->getDate(),
                    'active' => $ticket->isActive(),
                    'severity' => $ticket->getSeverity(),
                    'classification' => $ticket->getClassification(),
                    'updated_at' => $ticket->getUpdatedAt(),
                ];
            }, $tickets);

            return $this->json($data);
        } catch (PessimisticLockException $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param ManagerRegistry $connection
     * @param Request $request
     * @return JsonResponse
     *
     * @Route("/ticket/new", name="new_ticket", methods={"POST"})
     */
    public function new(ManagerRegistry $connection, Request $request): JsonResponse
    {
        $params = $request->toArray();

        $ticketRequestValidator = new RequestValidator();
        $ticketRequestValidator->isValid($params);

        /**
         * @var TicketRepository $repository
         */
        $repository = $connection->getRepository(Ticket::class);

        /**
         * @var Ticket $ticket
         */
        $ticket = new Ticket();
        $ticket->setActive($params['active'])
            ->setDescription($params['description'])
            ->setSeverity($params['severity'])
            ->setDate(new \DateTime());

        $repository->store($ticket, true);

        return $this->json([
            'message' => "Ticket id {$ticket->getId()} successfully stored."
        ]);
    }

    /**
     * @param ManagerRegistry $connection
     * @param int $id
     * @return JsonResponse
     *
     * @Route("/ticket/show/{id}", name="show_ticket", methods={"GET"})
     */
    public function show(ManagerRegistry $connection, int $id): JsonResponse
    {
        /**
         * @var TicketRepository $repository
         */
        $repository = $connection->getRepository(Ticket::class);

        /**
         * @var Ticket $ticket
         */
        $ticket = $repository->findOrFail($id);

        return $this->json([
            'id' => $ticket->getId(),
            'description' => $ticket->getDescription(),
            'date' => $ticket->getDate(),
            'active' => $ticket->isActive(),
            'severity' => $ticket->getSeverity(),
            'classification' => $ticket->getClassification(),
            'updated_at' => $ticket->getUpdatedAt(),
        ]);
    }

    /**
     * @param ManagerRegistry $connection
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     *
     * @Route("/ticket/{id}", name="edit_ticket", methods={"PUT"})
     */
    public function edit(ManagerRegistry $connection, Request $request, int $id): JsonResponse
    {
        $params = $request->toArray();

        $ticketRequestValidator = new RequestValidator();
        $ticketRequestValidator->isValid($params);

        /**
         * @var TicketRepository $repository
         */
        $repository = $connection->getRepository(Ticket::class);

        /**
         * @var Ticket $ticket
         */
        $ticket = $repository->findOrFail($id);

        $ticket->setActive($params['active'])
            ->setDescription($params['description'])
            ->setSeverity($params['severity'])
            ->setUpdatedAt(new \DateTimeImmutable());

        $repository->store($ticket, true);

        return $this->json([
            'message' => "Ticket id {$ticket->getId()} successfully updated."
        ]);
    }

    /**
     * @param ManagerRegistry $connection
     * @param int $id
     * @return JsonResponse
     * @throws NoResultException
     *
     * @Route("/ticket/{id}", name="ticket_delete", methods={"DELETE"})
     */
    public function delete(ManagerRegistry $connection, int $id): JsonResponse
    {
        /**
         * @var TicketRepository $repository
         */
        $repository = $connection->getRepository(Ticket::class);
        /**
         * @var Ticket $ticket
         */
        $ticket = $repository->findOrFail($id);

        $repository->remove($ticket, true);

        return $this->json("Ticket id {$id} successfully removed.");
    }

    /**
     * @param ManagerRegistry $connection
     * @param int $id
     * @return JsonResponse
     *
     * @Route("/ticket/classify/{id}", name="classify_ticket", methods={"GET"})
     */
    public function classify(ManagerRegistry $connection, int $id): JsonResponse
    {
        /**
         * @var TicketRepository $repository
         */
        $repository = $connection->getRepository(Ticket::class);
        /**
         * @var Ticket $ticket
         */
        $ticket = $repository->findOrFail($id);

        $validator = new Ticket\Validator($ticket);
        $ticket->setClassification($validator->classify());

        $repository->store($ticket, true);

        return $this->json("Ticket id {$id} successfully classified.");
    }
}
