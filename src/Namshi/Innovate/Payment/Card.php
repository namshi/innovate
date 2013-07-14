<?php

namespace Namshi\Innovate\Payment;

use InvalidArgumentException;
use DateTime;

/**
 * This class represents a card as detailed as Innovate needs it.
 */
class Card
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
     * @var string
     */
    protected $expiryMonth;

    /**
     * @var string
     */
    protected $expiryYear;

    /** Constructor
     *
     * @param string $number
     * @param string $cvv
     * @param \DateTime $expiryData
     */
    public function __construct($number, $cvv, DateTime $expiryData)
    {
        $this->setNumber($number);
        $this->setCvv($cvv);
        $this->setExpiryDate($expiryData);
    }

    /**
     * Sets expiry year and month
     *
     * @param \DateTime $expiryDate
     * @throws \InvalidArgumentException
     */
    public function setExpiryDate(DateTime $expiryDate)
    {
        if ($expiryDate->getTimestamp() - (new DateTime())->getTimestamp() < 0) {
            throw new InvalidArgumentException("The date parameter is expired.");
        }

        $this->setExpiryMonth($expiryDate->format('m'));
        $this->setExpiryYear($expiryDate->format('Y'));
    }

    /**
     * @param $number
     * @throws \InvalidArgumentException
     */
    public function setNumber($number)
    {
        if (!is_numeric($number)) {
            throw new InvalidArgumentException("The number parameter must be numeric.");
        }
        $this->number = $number;
    }

    /**
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param $cvv
     * @throws \InvalidArgumentException
     */
    public function setCvv($cvv)
    {
        if (!is_numeric($cvv)) {
            throw new InvalidArgumentException("The CVV parameter must be numeric.");
        }
        $this->cvv = $cvv;
    }

    /**
     * @return string
     */
    public function getCvv()
    {
        return $this->cvv;
    }

    /**
     * @param $expiryMonth
     * @throws \InvalidArgumentException
     */
    public function setExpiryMonth($expiryMonth)
    {

        if (!is_numeric($expiryMonth) || $expiryMonth < 1 || $expiryMonth > 12) {
            throw new InvalidArgumentException("The expiry month parameter must be a number between 1 - 12.");
        }
        $this->expiryMonth = $expiryMonth;
    }

    /**
     * @return string
     */
    public function getExpiryMonth()
    {
        return $this->expiryMonth;
    }

    /**
     * @param $expiryYear
     * @throws \InvalidArgumentException
     */
    public function setExpiryYear($expiryYear)
    {
        if (!is_numeric($expiryYear)) {
            throw new InvalidArgumentException("The number parameter must be numeric.");
        }
        $this->expiryYear = $expiryYear;
    }

    /**
     * @return string
     */
    public function getExpiryYear()
    {
        return $this->expiryYear;
    }

    /**
     * Converts the current object to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'number'    => $this->getNumber(),
            'cvv'       => $this->getCvv(),
            'expiry'    => array(
                'month' => $this->getExpiryMonth(),
                'year'  => $this->getExpiryYear(),
            )
        );
    }
}
