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
    
    /**
     * Constructor
     * 
     * @param string $type
     * @param string $class
     * @param bool $test
     */
    public function __construct($type, $class, $test)
    {
        $this->setType($type);
        $this->setClass($class);
        $this->setTest($test);
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
    
    /**
     * Converts the current object to an array.
     * 
     * @return array
     */
    public function toArray()
    {
        return array(
            'type'  => $this->getType(),
            'class' => $this->getClass(),
            'test'  => $this->getTest() ? 1 : 0,
        );
    }
}