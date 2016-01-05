<?php

namespace Namshi\Innovate\Payment;

use Namshi\Innovate\Payment\Billing\Customer;
use Namshi\Innovate\Payment\Billing\Address;
use Namshi\Innovate\AbstractCustomerInformation;
use InvalidArgumentException;

/**
 * This class represents a billing as detailed as Innovate needs it.
 */
class BillingInformation extends AbstractCustomerInformation
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
     * Constructor
     *
     * @param Billing\Customer $customer
     * @param Billing\Address $address
     * @param string $email
     * @param string $ip
     */
    public function __construct(Customer $customer, Address $address, $email, $ip)
    {
        parent::__construct($email, $ip);

        $this->customer = $customer;
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
     * @return Namshi\Innovate\Payment\Billing\Customer
     */
    public function getCustomer()
    {
        return $this->customer;
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
