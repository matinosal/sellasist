<?php
namespace App\Exception;

class StatusCodeException extends \Exception
{
    public function __construct(string $message = "", int $statusCode = 500)
    {
        parent::__construct($message, $statusCode);
    }
}