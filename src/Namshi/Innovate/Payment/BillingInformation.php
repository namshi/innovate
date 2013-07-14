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
    /**
     * @var Namshi\Innovate\Payment\Billing\Customer
     */
    protected $customer;

    /**
     * @var Namshi\Innovate\Payment\Billing\Address
     */
    protected $address;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $ip;

    /**
     * Constructor
     *
     * @param Billing\Customer $customer
     * @param Billing\Address $address
     * @param string $email
     * @param string $ip
     */
    public function __construct(Customer $customer, Address $address, $email, $ip)
    {
        $this->setCustomer($customer);
        $this->setAddress($address);
        $this->setEmail($email);
        $this->setIp($ip);
    }

    /**
     * @param Billing\Address $address
     */
    public function setAddress(Address $address)
    {
        $this->address = $address;
    }

    /**
     * @return Namshi\Innovate\Payment\Billing\Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param Billing\Customer $customer
     */
    public function setCustomer(Customer $customer)
    {
        $this->customer = $customer;
    }

    /**
     * @return Namshi\Innovate\Payment\Billing\Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param $email
     * @throws \InvalidArgumentException
     */
    public function setEmail($email)
    {
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("The email parameter doesn't match email format.");
        }
        
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param $ip
     * @throws \InvalidArgumentException
     */
    public function setIp($ip)
    {
        if (! filter_var($ip, FILTER_VALIDATE_IP)) {
            throw new InvalidArgumentException("The ip address parameter doesn't match ip format.");
        }
        $this->ip = $ip;
    }

    /**
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Converts the current object to an array.
     *
     * @return array
     */
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
