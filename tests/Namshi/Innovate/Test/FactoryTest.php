<?php

namespace Namshi\Innovate\Test;

use Namshi\Innovate\Payment\Transaction as PaymentTransaction;
use Namshi\Innovate\Payment\Card as PaymentCard;
use Namshi\Innovate\Request\Factory;
use Namshi\Innovate\Client;
use Namshi\Innovate\Test\ClientStub;
use Namshi\Innovate\Tokenized\Card;
use Namshi\Innovate\Tokenized\BillingInformation;
use Namshi\Innovate\Payment\BillingInformation as PaymentBillingInformation;
use Namshi\Innovate\Payment\Billing\Customer;
use Namshi\Innovate\Payment\Billing\Address;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testTokenize()
    {
        $factory = new Factory();
        $response = $factory->createTokenizeRequest(
            new ClientStub(),
            'EXAMPLE_STORE',
            'xyz',
            new Card('4000000000000002', new \DateTime('2025-5')),
            new BillingInformation(
                new Customer('Forenames', 'Surname', 'Mr'),
                new Address('STREET_ADDRESS_LINE_1', 'STREET_ADDRESS_LINE_2', 'STREET_ADDRESS_LINE_3', 'CITY', 'REGION', 'COUNTRY', '12345'),
                'test@namshi.com'
            )
        );

        $xml = new \SimpleXMLElement($response->getBody());
        $this->assertEquals('EXAMPLE_STORE', (string) $xml->store);
        $this->assertEquals('xyz', (string) $xml->key);
        $this->assertEquals('4000000000000002',(string) $xml->card->number);
        $this->assertEquals('05',(string) $xml->card->expiry->month);
        $this->assertEquals('2025',(string) $xml->card->expiry->year);
        $this->assertEquals('Forenames',(string) $xml->billing->name->first);
        $this->assertEquals('Surname',(string) $xml->billing->name->last);
        $this->assertEquals('STREET_ADDRESS_LINE_1',(string) $xml->billing->address->line1);
        $this->assertEquals('STREET_ADDRESS_LINE_2',(string) $xml->billing->address->line2);
        $this->assertEquals('STREET_ADDRESS_LINE_3',(string) $xml->billing->address->line3);
        $this->assertEquals('CITY',(string) $xml->billing->address->city);
        $this->assertEquals('REGION',(string) $xml->billing->address->region);
        $this->assertEquals('COUNTRY',(string) $xml->billing->address->country);
        $this->assertEquals('12345',(string) $xml->billing->address->zip);
        $this->assertEquals('test@namshi.com',(string) $xml->billing->email);
    }

    public function testCreateMpiRequest()
    {
        $factory = new Factory();

        $request = $factory->createMpiRequest(
            new ClientStub(),
            'POST',
            Client::INNOVATE_MPI_URL,
            null,
            'EXAMPLE_STORE',
            'xyz',
            new PaymentTransaction('sale', 'ecom', true, 'ORDER_NUMBER', 'DESCRIPTION', 'USD', 40),
            new PaymentCard('4000000000000002', '123', new \DateTime('2025-5')),
            new PaymentBillingInformation(
                new Customer('Forenames', 'Surname', 'Mr'),
                new Address('STREET_ADDRESS_LINE_1', 'STREET_ADDRESS_LINE_2', 'STREET_ADDRESS_LINE_3', 'CITY', 'REGION', 'COUNTRY', '12345'),
                'test@namshi.com',
                '192.168.0.1'
            )
        );
        $xml     = new \SimpleXMLElement($request->getBody());

        $this->assertEquals('xyz', (string) $xml->key);
        $this->assertEquals('sale', (string)  $xml->tran->type);
        $this->assertEquals('USD',(string)  $xml->tran->currency);
        $this->assertEquals('4000000000000002',(string)  $xml->card->number);
        $this->assertEquals('123',(string)  $xml->card->cvv);
        $this->assertEquals('05',(string)  $xml->card->expiry->month);
        $this->assertEquals('2025',(string)  $xml->card->expiry->year);
        $this->assertEquals('Forenames',(string)  $xml->billing->name->first);
        $this->assertEquals('Surname',(string)  $xml->billing->name->last);
        $this->assertEquals('Mr',(string)  $xml->billing->name->title);
        $this->assertEquals('STREET_ADDRESS_LINE_1',(string)  $xml->billing->address->line1);
        $this->assertEquals('STREET_ADDRESS_LINE_2',(string)  $xml->billing->address->line2);
        $this->assertEquals('STREET_ADDRESS_LINE_3',(string)  $xml->billing->address->line3);
        $this->assertEquals('CITY',(string)  $xml->billing->address->city);
        $this->assertEquals('REGION',(string)  $xml->billing->address->region);
        $this->assertEquals('COUNTRY',(string)  $xml->billing->address->country);
        $this->assertEquals('12345',(string)  $xml->billing->address->zip);
        $this->assertEquals('test@namshi.com',(string)  $xml->billing->email);
        $this->assertEquals('192.168.0.1',(string)  $xml->billing->ip);
    }

    public function testCreateSearchByCartIdRequest()
    {
        $factory = new Factory();
        $request = $factory->createSearchByCartIdRequest(
            new ClientStub(),
            'cardid',
            'merchantId',
            'searchKey'
        );

        $authString = base64_encode('merchantId:searchKey');

        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('https://secure.innovatepayments.com/tools/api/xml/transaction/cardid/cart', $request->getUrl());
        $this->assertEquals('Basic ' . $authString, (string)$request->getHeaders()->getAll()['authorization']);
    }
}
