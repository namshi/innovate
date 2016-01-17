<?php

namespace Namshi\Innovate\Tokenized;

use Namshi\Innovate\CreditCardInterface;

/**
 * Class Card represents a credit card.
 */
class Token implements CreditCardInterface
{
    /**
     * @var string
     */
    protected $number;

    /**
     * @var string
     */
    protected $cvv;

    /**
     * @param string $number
     * @param string $cvv
     */
    public function __construct($number, $cvv)
    {
        if (is_null($number)) {
            throw new \InvalidArgumentException("The token number cannot be null.");
        }

        if (!is_numeric($cvv)) {
            throw new \InvalidArgumentException("The CVV parameter must be numeric.");
        }

        $this->number = $number;
        $this->cvv     = $cvv;
    }

    /**
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @return string
     */
    public function getCvv()
    {
        return $this->cvv;
    }

    public function toArray()
    {
        return [
            'number' => $this->number,
            'cvv'    => $this->cvv
        ];
    }
}
