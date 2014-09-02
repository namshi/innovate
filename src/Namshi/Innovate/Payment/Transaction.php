<?php

namespace Namshi\Innovate\Payment;

use InvalidArgumentException;

/**
 * This class represents a transaction as detailed as Innovate needs it.
 */
class Transaction
{
    /**
     * Transaction type that identifies a sale.
     */
    const TYPE_SALE         = 'sale';

    /**
     * Class used to identity e-commerce transactions.
     */
    const CLASS_ECOMMERCE   = 'ecom';

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var bool
     */
    protected $test;

    /**
     * @var string
     */
    protected $cartId;

    /**
     * @var string
     */
    protected $currency;

    /**
     * @var float
     */
    protected $amount;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $ref;

    /**
     * Constructor
     *
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

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param string $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * @return bool
     */
    public function getTest()
    {
        return $this->test;
    }

    /**
     * @param bool $test
     * @throws \InvalidArgumentException
     */
    public function setTest($test)
    {
        if (!is_bool($test)) {
            throw new InvalidArgumentException("The test parameter must be a boolean");
        }
        
        $this->test = $test;
    }

    /**
     * @param float $amount
     * @throws \InvalidArgumentException
     */
    public function setAmount($amount)
    {
        if (! is_numeric($amount) || $amount <= 0) {
            throw new InvalidArgumentException("The amount parameter must be a number and more than zero.");
        }
        $this->amount = $amount;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param string $cartId
     */
    public function setCartId($cartId)
    {
        $this->cartId = $cartId;
    }

    /**
     * @return string
     */
    public function getCartId()
    {
        return $this->cartId;
    }


    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     * @throws \InvalidArgumentException
     */
    public function setCurrency($currency)
    {
        if (strlen($currency) != 3) {
            throw new InvalidArgumentException("The currency parameter must currency format.");
        }
        $this->currency = $currency;
    }

    /**
     * @param $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $ref
     */
    public function setRef($ref)
    {
        $this->ref = $ref;
    }

    /**
     * @return string
     */
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
