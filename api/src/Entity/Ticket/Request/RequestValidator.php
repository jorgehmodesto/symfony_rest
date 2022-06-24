<?php

namespace App\Entity\Ticket\Request;

use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RequestValidator
 * @package App\Entity\Ticket\Request
 */
class RequestValidator
{

    /**
     * @var array $required
     *   The request required fields.
     */
    private $required = [
        'description' => 'The ticket description must be provided',
        'active' => 'The ticket status must be provided',
        'severity' => 'The ticket severity must be provided',
    ];

    /**
     * Validates ticket request.
     *
     * @return $this
     * @throws ValidatorException
     */
    public function validate($params = [])
    {
        foreach ($this->required as $field => $message) {
            if (
                !array_key_exists($field, $params) ||
                empty($params[$field])
            ) {
                throw new ValidatorException($this->required[$field], Response::HTTP_BAD_REQUEST);
            }
        }

        return $this;
    }
}