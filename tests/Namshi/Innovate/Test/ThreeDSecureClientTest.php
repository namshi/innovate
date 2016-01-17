<?php

namespace Namshi\Innovate\Test;

use PHPUnit_Framework_TestCase;
use Namshi\Innovate\Client;
use Namshi\Innovate\Test\ClientStub;
use Namshi\Innovate\Payment\Transaction;
use Namshi\Innovate\Payment\Card;
use Namshi\Innovate\Payment\BillingInformation;
use Namshi\Innovate\Payment\Billing\Customer;
use Namshi\Innovate\Payment\Billing\Address;
use Namshi\Innovate\Payment\Browser;
use Namshi\Innovate\Tokenized\CustomerInformation;
use Namshi\Innovate\Tokenized\Token;

class ThreeDSecureClientTest extends PHPUnit_Framework_TestCase
{
    protected $mpiData = [
        'session' => 'xyz',
        'pares'   => '323213sdsd0132132='
    ];


    public function testPerform3DSecurePayment()
    {
        $client  = new Client('EXAMPLE_STORE', 'EXAMPLE_MERCHANT_ID', 'xyz', 'searchkey', '', null, new ClientStub());
        $response = $client->perform3DSecurePayment(
            new Transaction('sale', 'ecom', true, 'ORDER_NUMBER', 'DESCRIPTION', 'USD', 40),
            new Card('4000000000000002', '123', new \DateTime('2025-5')),
            new BillingInformation(
                new Customer('Forenames', 'Surname', 'Mr'),
                new Address('STREET_ADDRESS_LINE_1', 'STREET_ADDRESS_LINE_2', 'STREET_ADDRESS_LINE_3', 'CITY', 'REGION', 'COUNTRY', '12345'),
                'test@namshi.com',
                '192.168.0.1'
            ),
            new Browser('BROWSER_USER_AGENT_HEADER', 'BROWSER_ACCEPT_HEADER'),
            $this->mpiData
        );

        $this->assertEquals(200, $response->getStatusCode());

        $xml = new \SimpleXMLElement($response->getContent());

        $this->assertEquals('A', $xml->auth->status);
        $this->assertEquals('916358', $xml->auth->code);
        $this->assertEquals('Authorised', $xml->auth->message);
        $this->assertEquals('029818836006', $xml->auth->tranref);
        $this->assertEquals('Y', $xml->auth->cvv);
        $this->assertEquals('X', $xml->auth->avs);
        $this->assertEquals('4000/1635st7', $xml->auth->trace);
    }

    public function testPerform3DSecureTokenPayment()
    {
        $client    = new Client('EXAMPLE_STORE', 'EXAMPLE_MERCHANT_ID', 'xyz', 'searchkey', '', null, new ClientStub());
        $xmlSample = file_get_contents(__DIR__ . '/../../../fixtures/example_token_request.xml');
        $response = $client->perform3DSecurePayment(
            new Transaction('sale', 'ecom', true, 'ORDER_NUMBER', 'DESCRIPTION', 'USD', 40),
            new Token('1234123412340002', '123'),
            new CustomerInformation(
                'test@namshi.com',
                '192.168.0.1'
            ),
            new Browser('BROWSER_USER_AGENT_HEADER', 'BROWSER_ACCEPT_HEADER'),
            $this->mpiData
        );

        $this->assertEquals(200, $response->getStatusCode());

        $xml = new \SimpleXMLElement($response->getContent());

        $this->assertEquals('A', $xml->auth->status);
        $this->assertEquals('916358', $xml->auth->code);
        $this->assertEquals('Authorised', $xml->auth->message);
        $this->assertEquals('029818836006', $xml->auth->tranref);
        $this->assertEquals('Y', $xml->auth->cvv);
        $this->assertEquals('X', $xml->auth->avs);
        $this->assertEquals('4000/1635st7', $xml->auth->trace);
    }
}
