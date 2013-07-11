<?php

namespace Namshi\Innovate\Exception;

use Exception;

class AuthFailed extends Exception
{
    public function __construct()
    {
        $this->message = "Payment authentication failed";
    }
}
