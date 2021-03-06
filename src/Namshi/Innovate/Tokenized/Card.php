<?php

namespace Namshi\Innovate\Tokenized;

use InvalidArgumentException;
use DateTime;
use Namshi\Innovate\Exception\ExpiredCard as ExpiredCardException;

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
    public function __construct($number, DateTime $expiryData)
    {
        $this->setNumber($number);
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
        $currentDate  = new DateTime();
        $currentYear  = $currentDate->format('Y');
        $currentMonth = $currentDate->format('m');
        $expiryYear   = $expiryDate->format('Y');
        $expiryMonth  = $expiryDate->format('m');

        if ($currentYear > $expiryYear || ($currentYear == $expiryYear && $currentMonth > $expiryMonth)) {
            throw new ExpiredCardException("The date parameter is expired.");
        }

        $this->setExpiryMonth($expiryMonth);
        $this->setExpiryYear($expiryYear);
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
            'expiry'    => array(
                'month' => $this->getExpiryMonth(),
                'year'  => $this->getExpiryYear(),
            )
        );
    }
}
