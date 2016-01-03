<?php

namespace Namshi\Innovate\Tokenized;

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
     * Constructor
     *
     * @param Billing\Customer $customer
     * @param Billing\Address $address
     * @param string $email
     * @param string $ip
     */
    public function __construct(Customer $customer, Address $address, $email)
    {
        $this->setCustomer($customer);
        $this->setAddress($address);
        $this->setEmail($email);
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
     * Converts the current object to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'name'      => $this->getCustomer()->toArray(),
            'address'   => $this->getAddress()->toArray(),
            'email'     => $this->getEmail()
        );
    }
}
