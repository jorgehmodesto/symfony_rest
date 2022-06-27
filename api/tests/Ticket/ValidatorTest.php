<?php

namespace App\Tests\Ticket;


use App\Entity\Ticket;
use PHPUnit\Framework\TestCase;

/**
 * Class ValidatorTest
 * @package App\Tests\Ticket
 */
class ValidatorTest extends TestCase
{
    /**
     * Ticket classify.
     *
     * @param $expected
     * @param $date
     * @param $severity
     *
     * @dataProvider classifyDataProvider
     */
    public function testClassify($expected, $date, $severity)
    {
        $ticket = new Ticket();

        $ticket->setDate($date);
        $ticket->setSeverity($severity);

        $validator = new Ticket\Validator($ticket);

        $this->assertEquals($expected, $validator->classify());
    }

    /**
     * Ticket opened days.
     */
    public function testGetOpenedDays()
    {
        $ticket = new Ticket();

        $ticket->setDate(new \DateTime(date('Y-m-d', strtotime('-3 days'))));
        $validator = new Ticket\Validator($ticket);

        $this->assertEquals(3, $validator->getOpenedDays());
    }

    /**
     * testClassify data provider.
     *
     * @return array
     */
    public function classifyDataProvider()
    {
        return [
            [
                Ticket::CLASSIFY_NEW,
                new \DateTime(),
                1
            ],
            [
                Ticket::CLASSIFY_MINOR,
                new \DateTime(date('Y-m-d', strtotime('-3 days'))),
                2
            ],
            [
                Ticket::CLASSIFY_NORMAL,
                new \DateTime(date('Y-m-d', strtotime('-7 days'))),
                2
            ],
            [
                Ticket::CLASSIFY_URGENT,
                new \DateTime(date('Y-m-d', strtotime('-12 days'))),
                2
            ],
            [
                Ticket::CLASSIFY_CRITICAL,
                new \DateTime(),
                3
            ]
        ];
    }
}