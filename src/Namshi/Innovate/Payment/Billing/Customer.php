<?php

namespace Namshi\Innovate\Payment\Billing;
use InvalidArgumentException;

/**
 * This class represents a customer as detailed as Innovate needs it.
 */
class Customer
{
    const TITLE_MR      = "Mr";
    const TITLE_MISS    = "Miss";

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $firstName;

    /**
     * @var string
     */
    protected $lastName;

    /**
     * Constructor
     *
     * @param string $firstName
     * @param string $lastName
     * @param string $title
     */
    public function __construct($firstName, $lastName, $title = self::TITLE_MR)
    {
        $this->setFirstName($firstName);
        $this->setLastName($lastName);
        $this->setTitle($title);
    }

    /**
     * @param $firstName
     * @throws \InvalidArgumentException
     */
    public function setFirstName($firstName)
    {
        if (!$firstName)
        {
            throw new InvalidArgumentException("The first name parameter can't be empty.");
        }
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param $lastName
     * @throws \InvalidArgumentException
     */
    public function setLastName($lastName)
    {
        if (!$lastName)
        {
            throw new InvalidArgumentException("The last name parameter can't be empty.");
        }
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param $title
     * @throws \InvalidArgumentException
     */
    public function setTitle($title)
    {
        if (!$title)
        {
            throw new InvalidArgumentException("The name title parameter can't be empty.");
        }
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Converts the current object to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'title' => $this->getTitle(),
            'first' => $this->getFirstName(),
            'last'  => $this->getLastName(),
        );
    }
}
