<?php

namespace Namshi\Innovate\Test;

use Namshi\Innovate\Request\Factory;
use Namshi\Innovate\Test\ClientStub;
use Namshi\Innovate\Tokenized\Card;
use Namshi\Innovate\Tokenized\BillingInformation;
use Namshi\Innovate\Payment\Billing\Customer;
use Namshi\Innovate\Payment\Billing\Address;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testTokenize()
    {
        $factory = new Factory();
        $client = new ClientStub('EXAMPLE_STORE', 'EXAMPLE_MERCHANT_ID', 'xyz', 'searchkey');
        $request = $factory->createTokenizeRequest(
            $client,
            $client->getStoreId(),
            $client->getKey(),
            new Card('4000000000000002', new \DateTime('2025-5')),
            new BillingInformation(
                new Customer('Forenames', 'Surname', 'Mr'),
                new Address('STREET_ADDRESS_LINE_1', 'STREET_ADDRESS_LINE_2', 'STREET_ADDRESS_LINE_3', 'CITY', 'REGION', 'COUNTRY', '12345'),
                'test@namshi.com'
            )
        );

        $xml = new \SimpleXMLElement($request->getBody());
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
}
