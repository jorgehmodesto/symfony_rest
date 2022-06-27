<?php

namespace App\Tests\Ticket;


use App\Entity\Ticket\Request\RequestValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Exception\ValidatorException;

/**
 * Class RequestValidatorTest
 * @package App\Tests\Ticket
 */
class RequestValidatorTest extends TestCase
{
    /**
     * Validates that ValidatorException is gonna be thrown.
     *
     * @param string $message
     * @param array $params
     *
     * @dataProvider validateParamsFailProvider
     */
    public function testValidateParamsFail(string $message, array $params)
    {
        $requestValidator = new RequestValidator();

        $this->expectException(ValidatorException::class);
        $this->expectExceptionCode(Response::HTTP_BAD_REQUEST);
        $this->expectExceptionMessage($message);

        $requestValidator->isValid($params);
    }

    /**
     * Validates that it might be successful if the params are ok.
     */
    public function testValidateParamsSuccess()
    {
        $requestValidator = new RequestValidator();
        $assertion = $requestValidator->isValid([
            'description' => 'test',
            'active' => true,
            'severity' => 1
        ]);
        $this->assertTrue($assertion);
    }

    /**
     * testValidateParams data provider.
     *
     * @return array
     */
    public function validateParamsFailProvider()
    {
        return [
            [
                'message' => 'The ticket description must be provided',
                [],
            ],
            [
                'message' => 'The ticket status must be provided',
                ['description' => 'test'],
            ],
            [
                'message' => 'The ticket severity must be provided',
                [
                    'description' => 'test',
                    'active' => true,
                ],
            ],
        ];
    }
}