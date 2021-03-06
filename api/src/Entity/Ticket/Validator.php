<?php

namespace App\Entity\Ticket;

use App\Entity\Ticket;

/**
 * Class Validator
 * @package App\Entity\Ticket
 */
class Validator
{
    /**
     * @var Ticket $ticket
     *   Ticket object to be validated.
     */
    protected $ticket;

    /**
     * Validator constructor.
     * @param Ticket $ticket
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Classifies the ticket.
     *
     * @return string
     */
    public function classify()
    {
        $this->getOpenedDays();

        if ($this->ticket->getSeverity() === 2) {

            if ($this->getOpenedDays() > 10) {
                return Ticket::CLASSIFY_URGENT;
            }

            if ($this->getOpenedDays() > 5) {
                return Ticket::CLASSIFY_NORMAL;
            }

            if ($this->getOpenedDays() > 2) {
                return Ticket::CLASSIFY_MINOR;
            }
        }

        if ($this->ticket->getSeverity() === 3) {
            return Ticket::CLASSIFY_CRITICAL;
        }

        return Ticket::CLASSIFY_NEW;
    }

    /**
     * Calculates the number of days some ticket is not classified.
     *
     * @return int
     */
    public function getOpenedDays()
    {
        $ticketDate = date_create($this->ticket->getDate()->format('Y-m-d H:i:s'));
        $now = date_create(date('Y-m-d H:i:s'));

        $diff = date_diff($ticketDate, $now);

        return $diff->d;
    }
}