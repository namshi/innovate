<?php

namespace Namshi\Innovate\Test;

use PHPUnit_Framework_TestCase;
use Namshi\Innovate\Tokenized\CustomerInformation;
use InvalidArgumentException;

class CustomerInformationTest extends PHPUnit_Framework_TestCase
{
    public function testToArray()
    {
        $billing = new CustomerInformation('test.test@namshi.com', '192.168.0.1');
        $arr = $billing->toArray();

        $this->assertEquals('test.test@namshi.com', $arr['email']);
        $this->assertEquals('192.168.0.1', $arr['ip']);
    }

    public function testEmailParameterMatchesEmailFormat()
    {
        $billing = new CustomerInformation('test.test@namshi.com', '192.168.0.1');

        $this->assertEquals('test.test@namshi.com', $billing->getEmail());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testEmailParameterDoesntMatchesEmailFormat()
    {
        $billing = new CustomerInformation('test@--.com', '192.168.0.1');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testEmailParameterCantBeEmpty()
    {
        $billing = new CustomerInformation('', '192.168.0.1');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testIpAddressParameterCantBeEmpty()
    {
        $billing = new CustomerInformation('test.test@namshi.com', '');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testIpAddressParameterDoesntMatchIpFormat()
    {
        $billing = new CustomerInformation('test.test@namshi.com', '5432546');
    }
}
