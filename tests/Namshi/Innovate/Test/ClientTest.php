<?php

namespace Namshi\Innovate\Test;

use PHPUnit_Framework_TestCase;
use Namshi\Innovate\Client;
use Namshi\Innovate\Payment\Transaction;
use Namshi\Innovate\Payment\Card;
use Namshi\Innovate\Payment\BillingInformation;
use Namshi\Innovate\Payment\Billing\Customer;
use Namshi\Innovate\Payment\Billing\Address;
use Namshi\Innovate\Payment\Browser;
use DateTime;

class ClientTest extends PHPUnit_Framework_TestCase
{
    public function testGeneratingACorrectXmlBodyForTheRequest()
    {
        $client = new StubClient('EXAMPLE_STORE', 'EXAMPLE_MERCHANT_ID', 'xyz', 'searchkey');
        $xml    = file_get_contents(__DIR__ . '/../../../fixtures/example.xml');
        $result = $client->performPayment(
            new Transaction('sale', 'ecom', true, 'ORDER_NUMBER', 'DESCRIPTION', 'USD', 40),
            new Card('4000000000000002', '123', new DateTime('3013-5')),
            new BillingInformation(
                new Customer('Forenames', 'Surname', 'Mr'),
                new Address('STREET_ADDRESS_LINE_1', 'STREET_ADDRESS_LINE_2', 'STREET_ADDRESS_LINE_3', 'CITY', 'REGION', 'COUNTRY', '12345'),
                'test@namshi.com',
                '192.168.0.1'
            ),
            new Browser('BROWSER_USER_AGENT_HEADER', 'BROWSER_ACCEPT_HEADER')
        );
        $this->assertEquals(trim(preg_replace("/\s+/", '', $xml)), trim(preg_replace("/\s+/", '', $result->__toString())));
    }

    public function testClientRemoteUrl()
    {
        $this->assertInternalType('string', filter_var(Client::INNOVATE_URL, FILTER_VALIDATE_URL));
    }

    public function testClientRemoteMpiUrl()
    {
        $this->assertInternalType('string', filter_var(Client::INNOVATE_MPI_URL, FILTER_VALIDATE_URL));
    }
}

class StubClient extends Client
{
    public function performPayment(Transaction $transaction, Card $card, BillingInformation $billing, Browser $browser)
    {
        $this->setTransaction($transaction);
        $this->setCard($card);
        $this->setBillingInformation($billing);
        $this->setBrowser($browser);
        $request = parent::createRequest('post');
        $request->createBody($this->getStoreId(), $this->getKey(), $this->getTransaction(), $this->getCard(), $this->getBillingInformation(), $this->getBrowser(), array());

        return $request->getBody();
    }
}