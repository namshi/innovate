<?php

namespace Namshi\Innovate\Payment;

use InvalidArgumentException;

/**
 * This class represents a transaction as detailed as Innovate needs it.
 */
class Transaction
{
    protected $type;
    protected $class;
    protected $test;
    protected $cartId;
    protected $currency;
    protected $amount;
    protected $description;
    protected $ref;

    /**
     * @param string $type
     * @param string $class
     * @param bool $test
     * @param string $cartId
     * @param string $description
     * @param string $currency
     * @param float  $amount
     * @param null|string $ref
     */
    public function __construct($type, $class, $test, $cartId, $description, $currency, $amount, $ref = null)
    {
        $this->setType($type);
        $this->setClass($class);
        $this->setTest($test);
        $this->setCartId($cartId);
        $this->setDescription($description);
        $this->setCurrency($currency);
        $this->setAmount($amount);
        $this->setRef($ref);
    }
    
    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }
    
    public function getClass()
    {
        return $this->class;
    }

    public function setClass($class)
    {
        $this->class = $class;
    }
    
    public function getTest()
    {
        return $this->test;
    }

    public function setTest($test)
    {
        if (!is_bool($test)) {
            throw new InvalidArgumentException("The test parameter must be a boolean");
        }
        
        $this->test = $test;
    }

    public function setAmount($amount)
    {
        if (! is_numeric($amount) || $amount <= 0) {
            throw new InvalidArgumentException("The amount parameter must be a number and more than zero.");
        }
        $this->amount = $amount;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setCartId($cartId)
    {
        $this->cartId = $cartId;
    }

    public function getCartId()
    {
        return $this->cartId;
    }


    public function getCurrency()
    {
        return $this->currency;
    }

    public function setCurrency($currency)
    {
        if (strlen($currency) != 3) {
            throw new InvalidArgumentException("The currency parameter must currency format.");
        }
        $this->currency = $currency;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setRef($ref)
    {
        $this->ref = $ref;
    }

    public function getRef()
    {
        return $this->ref;
    }

    /**
     * Converts the current object to an array.
     * 
     * @return array
     */
    public function toArray()
    {
        return array(
            'type'        => $this->getType(),
            'class'       => $this->getClass(),
            'test'        => $this->getTest() ? 1 : 0,
            'cartid'      => $this->getCartId(),
            'description' => $this->getDescription(),
            'currency'    => $this->getCurrency(),
            'amount'      => $this->getAmount(),
            'ref'         => $this->getRef(),
        );
    }
}