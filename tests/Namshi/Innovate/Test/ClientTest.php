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
use Namshi\Innovate\Request\Factory;
use Prophecy\Argument;

class ClientTest extends PHPUnit_Framework_TestCase
{
    public function testGeneratingACorrectXmlBodyForTheRequest()
    {
        $factory        = new Factory();
        $factoryRequest = $factory->create("POST", "http://example.com", Argument::any(), Argument::any(), []);
        $guzzleClient = $this->prophesize('Guzzle\Service\Client');
        $guzzleClient->createRequest("POST", Client::INNOVATE_URL, Argument::any(), Argument::any(), Argument::any())
            ->shouldBeCalled()
            ->willReturn($factoryRequest);

        $guzzleClient->setRequestFactory($factory)->shouldBeCalled();

        $client = new ClientStub('EXAMPLE_STORE', 'EXAMPLE_MERCHANT_ID', 'xyz', 'searchkey', $guzzleClient->reveal());
        $xmlSample    = file_get_contents(__DIR__ . '/../../../fixtures/example.xml');

        $request = $client->performPayment(
            new Transaction('sale', 'ecom', true, 'ORDER_NUMBER', 'DESCRIPTION', 'USD', 40),
            new Card('4000000000000002', '123', new \DateTime('2025-5')),
            new BillingInformation(
                new Customer('Forenames', 'Surname', 'Mr'),
                new Address('STREET_ADDRESS_LINE_1', 'STREET_ADDRESS_LINE_2', 'STREET_ADDRESS_LINE_3', 'CITY', 'REGION', 'COUNTRY', '12345'),
                'test@namshi.com',
                '192.168.0.1'
            ),
            new Browser('BROWSER_USER_AGENT_HEADER', 'BROWSER_ACCEPT_HEADER')
        );

        $xml       = new \SimpleXMLElement($request->getBody());
        $xmlSample = new \SimpleXMLElement($xmlSample);

        $this->assertEquals($xmlSample->key,  $xml->key);
        $this->assertEquals($xmlSample->tran->type,  $xml->tran->type);
        $this->assertEquals($xmlSample->tran->currency, $xml->tran->currency);
        $this->assertEquals($xmlSample->card->number, $xml->card->number);
        $this->assertEquals($xmlSample->card->cvv,  $xml->card->cvv);
        $this->assertEquals($xmlSample->card->expiry->month,  $xml->card->expiry->month);
        $this->assertEquals($xmlSample->card->expiry->year, $xml->card->expiry->year);
    }

    public function testCreateMpiRequest()
    {
        $factory = new Factory();

        $guzzleClient = $this->prophesize('Guzzle\Service\Client');
        $guzzleClient->createRequest("POST", Client::INNOVATE_MPI_URL, null, null)->shouldBeCalled()
            ->willReturn($factory->create("POST", "http://example.com", Argument::any(), Argument::any(), []));
        $guzzleClient->setRequestFactory($factory)->shouldBeCalled();

        $client = new Client('EXAMPLE_STORE', 'EXAMPLE_MERCHANT_ID', 'xyz', 'searchkey', $guzzleClient->reveal());
        $this->setTransactionDetails($client);

        $request = $client->createMpiRequest('POST', Client::INNOVATE_MPI_URL, null);
        $xml     = new \SimpleXMLElement($request->getBody());

        $this->assertEquals('xyz', (string) $xml->key);
        $this->assertEquals('sale', (string)  $xml->tran->type);
        $this->assertEquals('USD',(string)  $xml->tran->currency);
        $this->assertEquals('4000000000000002',(string)  $xml->card->number);
        $this->assertEquals('123',(string)  $xml->card->cvv);
        $this->assertEquals('05',(string)  $xml->card->expiry->month);
        $this->assertEquals('2025',(string)  $xml->card->expiry->year);

    }

    public function testClientRemoteUrl()
    {
        $this->assertInternalType('string', filter_var(Client::INNOVATE_URL, FILTER_VALIDATE_URL));
    }

    public function testClientRemoteMpiUrl()
    {
        $this->assertInternalType('string', filter_var(Client::INNOVATE_MPI_URL, FILTER_VALIDATE_URL));
    }

    protected function setTransactionDetails(Client $client)
    {
        $client->setTransaction(new Transaction('sale', 'ecom', true, 'ORDER_NUMBER', 'DESCRIPTION', 'USD', 40));
        $client->setCard(new Card('4000000000000002', '123', new \DateTime('2025-5')));
        $client->setBillingInformation(new BillingInformation(
          new Customer('Forenames', 'Surname', 'Mr'),
          new Address('STREET_ADDRESS_LINE_1', 'STREET_ADDRESS_LINE_2', 'STREET_ADDRESS_LINE_3', 'CITY', 'REGION', 'COUNTRY', '12345'),
          'test@namshi.com',
          '192.168.0.1'
        ));

        $client->setBrowser(new Browser('BROWSER_USER_AGENT_HEADER', 'BROWSER_ACCEPT_HEADER'));
    }
}
