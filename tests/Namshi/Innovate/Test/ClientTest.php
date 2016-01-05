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
use Namshi\Innovate\Tokenized\Card as TokenizedCard;
use Namshi\Innovate\Tokenized\BillingInformation as TokenizedBillingInformation;

class ClientTest extends PHPUnit_Framework_TestCase
{
    public function testPerformPayment()
    {
        $client    = new Client('EXAMPLE_STORE', 'EXAMPLE_MERCHANT_ID', 'xyz', 'searchkey', '', null, new ClientStub());
        $response  = $client->performPayment(
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

    public function testPerformTokenPayment()
    {
        $client    = new Client('EXAMPLE_STORE', 'EXAMPLE_MERCHANT_ID', 'xyz', 'searchkey', '', null, new ClientStub());
        $response = $client->performPayment(
            new Transaction('sale', 'ecom', true, 'ORDER_NUMBER', 'DESCRIPTION', 'USD', 40),
            new Token('1234123412340002', '123'),
            new CustomerInformation(
                'test@namshi.com',
                '192.168.0.1'
            ),
            new Browser('BROWSER_USER_AGENT_HEADER', 'BROWSER_ACCEPT_HEADER')
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

    public function testPerformTokenPaymentWrongCreditCardData()
    {
        $client    = new Client('EXAMPLE_STORE', 'EXAMPLE_MERCHANT_ID', 'xyz', 'searchkey', '', null, new ClientStub());
        $response  = $client->performPayment(
            new Transaction('sale', 'ecom', true, 'ORDER_NUMBER', 'DESCRIPTION', 'USD', 40),
            new Card('4000000000000002', '999', new \DateTime('2025-5')),
            new BillingInformation(
                new Customer('Forenames', 'Surname', 'Mr'),
                new Address('STREET_ADDRESS_LINE_1', 'STREET_ADDRESS_LINE_2', 'STREET_ADDRESS_LINE_3', 'CITY', 'REGION', 'COUNTRY', '12345'),
                'test@namshi.com',
                '192.168.0.1'
            ),
            new Browser('BROWSER_USER_AGENT_HEADER', 'BROWSER_ACCEPT_HEADER')
        );

        $xml = new \SimpleXMLElement($response->getContent());

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('Invalid Request', $xml->error);
    }

    public function testSearchTransactionsByCartId()
    {
        $client = new Client('EXAMPLE_STORE', 'EXAMPLE_MERCHANT_ID', 'xyz', 'searchkey', '', null, new ClientStub());
        $xml    = $client->searchTransactionsByCartId('cartIDcart');

        $this->assertEquals('1', (string)$xml->trancount);
        $this->assertEquals('00000001', (string)$xml->transaction->id);
    }

    public function testTokenize()
    {
        $client   = new Client('EXAMPLE_STORE', 'EXAMPLE_MERCHANT_ID', 'xyz', 'searchkey', '', null, new ClientStub());
        $response = $client->tokenize(
            new TokenizedCard('4000000000000002', new \DateTime('2025-5')),
            new TokenizedBillingInformation(
                new Customer('Forenames', 'Surname', 'Mr'),
                new Address('STREET_ADDRESS_LINE_1', 'STREET_ADDRESS_LINE_2', 'STREET_ADDRESS_LINE_3', 'CITY', 'REGION', 'COUNTRY', '12345'),
                'test@namshi.com'
            )
        );

        $xml = $response->xml();
        $this->assertEquals('1', (string)$xml->result);
        $this->assertEquals('8656000537260002', (string)$xml->token->ref);
        $this->assertEquals('Visa Credit ending 0002', (string)$xml->token->description);
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
