<?php

namespace Namshi\Innovate\Payment\Billing;
use InvalidArgumentException;

/**
 * This class represents a customer as detailed as Innovate needs it.
 */
class Customer
{
    protected $title;
    protected $firstName;
    protected $lastName;

    /**
     * Constructor
     *
     * @param string $title
     * @param string $firstName
     * @param string $lastName
     */
    public function __construct($title, $firstName, $lastName)
    {
        $this->setTitle($title);
        $this->setFirstName($firstName);
        $this->setLastName($lastName);
    }

    public function setFirstName($firstName)
    {
        if (!$firstName)
        {
            throw new InvalidArgumentException("The first name parameter can't be empty.");
        }
        $this->firstName = $firstName;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function setLastName($lastName)
    {
        if (!$lastName)
        {
            throw new InvalidArgumentException("The last name parameter can't be empty.");
        }
        $this->lastName = $lastName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function setTitle($title)
    {
        if (!$title)
        {
            throw new InvalidArgumentException("The name title parameter can't be empty.");
        }
        $this->title = $title;
    }

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
