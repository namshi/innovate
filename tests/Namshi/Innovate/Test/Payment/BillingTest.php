<?php

namespace Namshi\Innovate\Test;

use PHPUnit_Framework_TestCase;
use Namshi\Innovate\Payment\BillingInformation;
use Namshi\Innovate\Payment\Billing\Customer;
use Namshi\Innovate\Payment\Billing\Address;
use InvalidArgumentException;

class BillingTest extends PHPUnit_Framework_TestCase
{
    protected function getCustomer()
    {
        return new Customer('test', 'test', 'Mr');
    }

    protected function getAddress()
    {
        return new Address('Al qouz', 'Golden Daemond Park', 'Building 3', 'Dubai', 'region', 'UAE');
    }

    public function testRetrievingCorrectDataByToArrayFunction()
    {
        $billing = new BillingInformation($this->getCustomer(), $this->getAddress(), 'test.test@namshi.com', '192.168.0.1');
        $arr = $billing->toArray();

        $this->assertEquals('Mr', $arr['name']['title']);
        $this->assertEquals('test', $arr['name']['first']);
        $this->assertEquals('test', $arr['name']['last']);
        $this->assertEquals('Al qouz', $arr['address']['line1']);
        $this->assertEquals('Golden Daemond Park', $arr['address']['line2']);
        $this->assertEquals('Building 3', $arr['address']['line3']);
        $this->assertEquals('Dubai', $arr['address']['city']);
        $this->assertEquals('region', $arr['address']['region']);
        $this->assertEquals('UAE', $arr['address']['country']);
        $this->assertEquals('test.test@namshi.com', $arr['email']);
    }

    public function testEmailParameterMatchesEmailFormat()
    {
        $billing = new BillingInformation($this->getCustomer(), $this->getAddress(), 'test.test@namshi.com', '192.168.0.1');

        $this->assertEquals('test.test@namshi.com', $billing->getEmail());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testEmailParameterDoesntMatchesEmailFormat()
    {
        $billing = new BillingInformation($this->getCustomer(), $this->getAddress(), 'test@--.com', '192.168.0.1');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testEmailParametercantBeEmpty()
    {
        $billing = new BillingInformation($this->getCustomer(), $this->getAddress(), '', '192.168.0.1');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testIpAddressParametercantBeEmpty()
    {
        $billing = new BillingInformation($this->getCustomer(), $this->getAddress(), 'test.test@namshi.com', '');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testIpAddressParameterDoesntmatchIpFormat()
    {
        $billing = new BillingInformation($this->getCustomer(), $this->getAddress(), 'test.test@namshi.com', '5432546');
    }

    public function testAddressZipCodeCanBeNullInBillingAddress()
    {
        $address = new Address('Al qouz', 'Golden Daemond Park', 'Building 3', 'Dubai', 'region', 'UAE');

        $this->assertEquals('', $address->getZip());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testAddressZipCodeCantBeString()
    {
        new Address('Al qouz', 'Golden Daemond Park', 'Building 3', 'Dubai', 'region', 'UAE', 'zip');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testAddressCityParameterCantBeEmpty()
    {
        new Address('Al qouz', 'Golden Daemond Park', 'Building 3', '', 'region', 'UAE');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testAddressCountryParameterCantBeEmpty()
    {
        new Address('Al qouz', 'Golden Daemond Park', 'Building 3', 'Dubai', 'region', '');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testAddressLine1ParameterCantBeEmpty()
    {
        new Address('', 'Golden Daemond Park', 'Building 3', 'Dubai', 'region', 'UAE');
    }

    public function testAddressNotMandatoryParametersCanBeNull()
    {
        $address = new Address('Al qouz', null, null, 'Dubai', null, 'UAE');

        $this->assertEquals('', $address->getZip());
        $this->assertEquals('', $address->getLine2());
        $this->assertEquals('', $address->getLine3());
        $this->assertEquals('', $address->getRegion());
        $this->assertEquals('Dubai', $address->getCity());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testCustomerFirstNameCantBeEmpty()
    {
        new Customer('Mr', '', 'Last');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testCustomerLastNameCantBeEmpty()
    {
        new Customer('Mr', 'first', '');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testCustomerTitleNameCantBeEmpty()
    {
        new Customer('', 'first', 'last');
    }
}
