<?php

namespace Namshi\Innovate\Payment;

use Namshi\Innovate\Payment\Billing\Customer;
use Namshi\Innovate\Payment\Billing\Address;
use InvalidArgumentException;

/**
 * This class represents a billing as detailed as Innovate needs it.
 */
class BillingInformation
{
    protected $customer;
    protected $address;
    protected $email;
    protected $ip;

    public function __construct(Customer $customer, Address $address, $email, $ip)
    {
        $this->setCustomer($customer);
        $this->setAddress($address);
        $this->setEmail($email);
        $this->setIp($ip);
    }

    public function setAddress(Address $address)
    {
        $this->address = $address;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function setCustomer(Customer $customer)
    {
        $this->customer = $customer;
    }

    public function getCustomer()
    {
        return $this->customer;
    }

    public function setEmail($email)
    {
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("The email parameter doesn't match email format.");
        }
        
        $this->email = $email;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setIp($ip)
    {
        if (! filter_var($ip, FILTER_VALIDATE_IP)) {
            throw new InvalidArgumentException("The ip address parameter doesn't match ip format.");
        }
        $this->ip = $ip;
    }

    public function getIp()
    {
        return $this->ip;
    }

    public function toArray()
    {
        return array(
            'name'      => $this->getCustomer()->toArray(),
            'address'   => $this->getAddress()->toArray(),
            'email'     => $this->getEmail(),
            'ip'        => $this->getIp(),
        );
    }
}
