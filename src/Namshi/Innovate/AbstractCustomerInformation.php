<?php

namespace Namshi\Innovate;

abstract class AbstractCustomerInformation implements CustomerInformationInterface
{
    protected $email;

    protected $ip;

    public function __construct($email, $ip)
    {
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("The email parameter doesn't match email format.");
        }

        if (! filter_var($ip, FILTER_VALIDATE_IP)) {
            throw new \InvalidArgumentException("The ip address parameter doesn't match ip format.");
        }

        $this->email = $email;
        $this->ip    = $ip;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getIp()
    {
        return $this->ip;
    }

    abstract public function toArray();
}
