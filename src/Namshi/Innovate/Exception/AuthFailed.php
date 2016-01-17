<?php

namespace Namshi\Innovate\Exception;

use Exception;

/**
 * Exception responsible to report authentication failed in innovate client
 */
class AuthFailed extends Exception
{
    public function __construct($message = null)
    {
        $this->message = $message ?: "Payment authentication failed";
    }
}
