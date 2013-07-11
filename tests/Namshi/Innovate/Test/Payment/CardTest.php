<?php

namespace Namshi\Innovate\Test;

use PHPUnit_Framework_TestCase;
use Namshi\Innovate\Payment\Card;
use InvalidArgumentException;
use DateTime;
use DateInterval;

class CardTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function testTheNumberCantBeAString()
    {
        new Card('number', '123', new DateTime('2013-2'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testTheDateParameterCantBeExpired()
    {
        new Card(99999, '123', new DateTime('2013-1'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testTheCVVCantBeAString()
    {
        new Card('1111111111111', 'cvv', new DateTime('2013-2'));
    }

    public function testAllParametersSetCorrectly()
    {
        $expiryDate = (new DateTime())->add(new DateInterval('P1M'));
        $card       = new Card('51515151', '123', $expiryDate);
        $arr        = $card->toArray();

        $this->assertEquals('51515151', $arr['number']);
        $this->assertEquals('123', $arr['cvv']);
        $this->assertEquals($expiryDate->format('Y'), $arr['expiry']['year']);
        $this->assertEquals($expiryDate->format('m'), $arr['expiry']['month']);
    }
}