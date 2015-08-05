<?php

namespace Namshi\Innovate\Test;

use PHPUnit_Framework_TestCase;
use Namshi\Innovate\Test\ClientStub;
use Namshi\Innovate\Payment\Transaction;
use Namshi\Innovate\Payment\Card;
use Namshi\Innovate\Payment\BillingInformation;
use Namshi\Innovate\Payment\Billing\Customer;
use Namshi\Innovate\Payment\Billing\Address;
use Namshi\Innovate\Payment\Browser;

class ThreeDSecureClientTest extends PHPUnit_Framework_TestCase
{
    public function testGeneratingACorrectXmlBodyForTheRequest()
    {
        $this->markTestSkipped('to be refactored');
        $mpiData     = array(
            'session' => 'xyz',
            'pares'   => '323213sdsd0132132='
        );

        $client = new ClientStub('EXAMPLE_STORE', 'EXAMPLE_MERCHANT_ID', 'xyz', 'searchkey');
        $request = $client->perform3DSecurePayment(
            new Transaction('sale', 'ecom', true, 'ORDER_NUMBER', 'DESCRIPTION', 'USD', 40),
            new Card('4000000000000002', '123', new \DateTime('2025-5')),
            new BillingInformation(
                new Customer('Forenames', 'Surname', 'Mr'),
                new Address('STREET_ADDRESS_LINE_1', 'STREET_ADDRESS_LINE_2', 'STREET_ADDRESS_LINE_3', 'CITY', 'REGION', 'COUNTRY', '12345'),
                'test@namshi.com',
                '192.168.0.1'
            ),
            new Browser('BROWSER_USER_AGENT_HEADER', 'BROWSER_ACCEPT_HEADER'),
            $mpiData
        );

        $xml = new \SimpleXMLElement($request->getBody());

        $this->assertEquals('xyz', (string) $xml->mpi->session);
        $this->assertEquals('323213sdsd0132132=', (string)  $xml->mpi->pares);
        $this->assertEquals('EXAMPLE_STORE',(string)  $xml->store);
        $this->assertEquals('xyz', (string) $xml->key);
        $this->assertEquals('sale', (string)  $xml->tran->type);
        $this->assertEquals('USD',(string)  $xml->tran->currency);
        $this->assertEquals('4000000000000002',(string)  $xml->card->number);
        $this->assertEquals('123',(string)  $xml->card->cvv);
        $this->assertEquals('05',(string)  $xml->card->expiry->month);
        $this->assertEquals('2025',(string)  $xml->card->expiry->year);
    }
}
