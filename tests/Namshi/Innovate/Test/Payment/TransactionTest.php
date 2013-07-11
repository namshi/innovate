<?php

namespace Namshi\Innovate\Test;

use PHPUnit_Framework_TestCase;
use Namshi\Innovate\Payment\Transaction;

class TransactionTest extends PHPUnit_Framework_TestCase
{
    public function testTheTestParameterCanBeOne()
    {
         $transaction  = new Transaction('sale', 'ecom', true, 'ORDER_NUMBER', 'DESCRIPTION', 'USD', 20);
         
         $this->assertEquals(1, $transaction->getTest());
    }

    public function testTheTestParameterCanBeZero()
    {
         $transaction  = new Transaction('sale', 'ecom', false, 'ORDER_NUMBER', 'DESCRIPTION', 'USD', 20);
         
         $this->assertEquals(0, $transaction->getTest());
    }

    public function testExportingTheTestParameterToArrayConvertsItintoEither1Or0()
    {
         $transaction  = new Transaction('sale', 'ecom', true, 'ORDER_NUMBER', 'DESCRIPTION', 'USD', 20);
         $arr = $transaction->toArray();
         
         $this->assertEquals(1, $arr['test']);
         
         $transaction  = new Transaction('sale', 'ecom', false, 'ORDER_NUMBER', 'DESCRIPTION', 'USD', 20);
         $arr = $transaction->toArray();
         
         $this->assertEquals(0, $arr['test']);
    }

    public function testAllParametersSetCorrectly()
    {
        $transaction  = new Transaction('sale', 'ecom', true, 'ORDER_NUMBER', 'DESCRIPTION', 'USD', 20);
        $arr = $transaction->toArray();

        $this->assertEquals('sale', $arr['type']);
        $this->assertEquals('ORDER_NUMBER', $arr['cartid']);
        $this->assertEquals('DESCRIPTION', $arr['description']);
        $this->assertEquals(1, $arr['test']);
        $this->assertEquals('USD', $arr['currency']);
        $this->assertEquals(20, $arr['amount']);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testTheTestParameterCantBeAnInteger()
    {
         new Transaction('sale', 'ecom', 12, 'ORDER_NUMBER', 'DESCRIPTION', 'USD', 20);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testTheTestParameterCantBeAString()
    {
         new Transaction('sale', 'ecom', '12', 'ORDER_NUMBER', 'DESCRIPTION', 'USD', 20);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testTheAmountParameterCantBeAString()
    {
        new Transaction('sale', 'ecom', true, 'ORDER_NUMBER', 'DESCRIPTION', 'USD', 'invalid');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testTheAmountParameterCantBeNegative()
    {
        new Transaction('sale', 'ecom', true, 'ORDER_NUMBER', 'DESCRIPTION', 'USD', -10);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testTheAmountParameterCantBeZero()
    {
        new Transaction('sale', 'ecom', true, 'ORDER_NUMBER', 'DESCRIPTION', 'USD', 0);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testTheCurrencyParameterLengthCantBeMoreThanThree()
    {
        new Transaction('sale', 'ecom', true, 'ORDER_NUMBER', 'DESCRIPTION', 'USDDSA', 15);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testTheCurrencyParameterLengthCantBeLessThanThree()
    {
        new Transaction('sale', 'ecom', true, 'ORDER_NUMBER', 'DESCRIPTION', 'US', 15);
    }
}