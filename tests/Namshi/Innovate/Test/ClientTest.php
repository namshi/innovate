<?php

namespace Namshi\Innovate\Test;

use PHPUnit_Framework_TestCase;
use Namshi\Innovate\Client;
use Namshi\Innovate\Payment\Transaction;

class ClientTest extends PHPUnit_Framework_TestCase
{
    public function testGeneratingACorrectXmlBodyForTheRequest()
    {
        $client = new StubClient('EXAMPLE_STORE', 'xyz', new Transaction('sale', 'ecom', true));
        $xml    = simplexml_load_file(__DIR__ . '/../../../fixtures/example.xml');
        $result = simplexml_load_string($client->performPayment());
        
        $this->assertEquals($xml->store, $result->store);
        $this->assertEquals($xml->key, $result->key);
        $this->assertEquals($xml->tran->type, $result->tran->type);
        $this->assertEquals($xml->tran->class, $result->tran->class);
        $this->assertEquals($xml->tran->test, $result->tran->test);
    }
}

class StubClient extends Client
{
    public function send($request)
    {
        return $request->getBody();
    }
}