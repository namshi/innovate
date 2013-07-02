<?php

namespace Namshi\Innovate\Test;

use PHPUnit_Framework_TestCase;
use Namshi\Innovate\Payment\Transaction;

class TransactionTest extends PHPUnit_Framework_TestCase
{
    public function testTheTestParameterCanBeOne()
    {
         $transaction  = new Transaction('sale', 'ecom', true);
         
         $this->assertEquals(1, $transaction->getTest());
    }

    public function testTheTestParameterCanBeZero()
    {
         $transaction  = new Transaction('sale', 'ecom', false);
         
         $this->assertEquals(0, $transaction->getTest());
    }

    public function testExportingTheTestParameterToArrayConvertsItintoEither1Or0()
    {
         $transaction  = new Transaction('sale', 'ecom', true);
         $arr = $transaction->toArray();
         
         $this->assertEquals(1, $arr['test']);
         
         $transaction  = new Transaction('sale', 'ecom', false);
         $arr = $transaction->toArray();
         
         $this->assertEquals(0, $arr['test']);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testTheTestParameterCantBeAnInteger()
    {
         new Transaction('sale', 'ecom', 12);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testTheTestParameterCantBeAString()
    {
         new Transaction('sale', 'ecom', '12');
    }
}