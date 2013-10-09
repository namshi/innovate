<?php

namespace Namshi\Innovate\Integration\Test;

use PHPUnit_Framework_TestCase;
use Namshi\Innovate\Payment\Transaction;
use Namshi\Innovate\Payment\BillingInformation;
use Namshi\Innovate\Payment\Browser;
use Namshi\Innovate\Payment\Billing\Customer;
use Namshi\Innovate\Payment\Billing\Address;
use Namshi\Innovate\Payment\Card;
use Namshi\Innovate\Client;
use DateTime;
use DateInterval;
use Namshi\Innovate\Http\Response\Redirect;
use DOMXPath;
use DOMDocument;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class ServiceTest extends PHPUnit_Framework_TestCase
{
    protected $client;
    protected $configs;
    protected $cardInfo;
    protected $redirectUrlCardInfo;
    protected $transaction;
    protected $card;
    protected $billing;
    protected $browser;
    protected $customer;
    protected $address;
    protected $ip;

    public function setUp()
    {
        $realConfigFile = __DIR__ . '/../../../../../.innovate.config';

        if (file_exists($realConfigFile)) {
            require $realConfigFile;
            $this->configs              = $configs;
            $this->cardInfo             = $cardInfo;
            $this->redirectUrlCardInfo  = $redirectUrlCardInfo;
            $this->ip                   = $ip;
        } else {
            throw new FileNotFoundException($realConfigFile);
        }

        $this->transaction  = new Transaction('sale', 'ecom', true, 'ORDER_NUMBER', 'DESCRIPTION', 'AED', 40, '');
        $this->card         = new Card($this->cardInfo['number'], $this->cardInfo['cvv'], (new DateTime())->add(new DateInterval('P1M')));
        $this->customer     = new Customer('Ayham', 'Alzoubi', 'Mr');
        $this->address      = new Address('alqouz', 'gdp', 'byuilding 3', 'dubai', 'gcc', 'AE', '00971');
        $this->billing      = new BillingInformation($this->customer, $this->address, 'test+test@namshi.com', '86.98.16.162');
        $this->browser      = new Browser('agent', 'accept');
        $this->client       = new Client($this->configs['storeId'], $this->configs['authenticationKey'], $this->configs);
    }

    public function testInnovateServiceAuthentication()
    {
        $response = $this->client->performPayment($this->transaction, $this->card, $this->billing, $this->browser);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testInnovateServiceAuthenticationFailingWithWrongStoreId()
    {
        $client   = $this->client;
        $client->setStoreId('wrong id');
        $response = $client->performPayment($this->transaction, $this->card, $this->billing, $this->browser);
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testInnovateServiceAuthenticationFailingWithWrongAuthenticationKey()
    {
        $client   = $this->client;
        $client->setKey('wrong key');
        $response = $client->performPayment($this->transaction, $this->card, $this->billing, $this->browser);
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testInnovateServiceAuthenticationFailingWithInvalidCardInfo()
    {
        $response = $this->client->performPayment($this->transaction, new Card('12122123323', '3222', new DateTime('2014-5')), $this->billing, $this->browser);
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testInnovateServiceRedirectUrl()
    {
        $this->card = new Card($this->redirectUrlCardInfo['number'], $this->redirectUrlCardInfo['cvv'], new DateTime('2014-5'));
        $response   = $this->client->performPayment($this->transaction, $this->card, $this->billing, $this->browser);
        $this->assertInstanceOf('Namshi\Innovate\Http\Response\Redirect', $response);

        if ($response instanceof Redirect) {

            $request    = $this->client->createRequest('post', $response->getTargetUrl(), array(), $this->getAcsForm($response), array('CONTENT-TYPE' => 'text/form-data', 'ACCEPT' => 'application/x-www-form-urlencoded'));
            $response   = $this->client->send($request);
            $content    = new DOMDocument();
            $contentTxt = $response->getBody()->__toString();
            libxml_use_internal_errors(true);
            $content->loadHTML($contentTxt);
            $xpath      = new DOMXPath($content);
            $mpiFinal   = array(
                'PaRes'     => $xpath->query("//input[@name='PaRes']")->item(0)->getAttribute('value'),
                'session'   => $xpath->query("//input[@name='MD']")->item(0)->getAttribute('value'),
            );
            $response   = $this->client->send($this->client->createRemoteRequest('POST', Client::INNOVATE_URL, null, null, $mpiFinal));

            $this->assertEquals(200, $response->getStatusCode());
        }
    }

    public function testInnovateServiceRedirectUrlWithoutSendingFormData()
    {
        $this->card = new Card($this->redirectUrlCardInfo['number'], $this->redirectUrlCardInfo['cvv'], new DateTime('2014-5'));
        $response   = $this->client->performPayment($this->transaction, $this->card, $this->billing, $this->browser);
        $this->assertInstanceOf('Namshi\Innovate\Http\Response\Redirect', $response);

        if ($response instanceof Redirect) {
            $response = $this->client->authorizeRemoteRequest(array());

            $this->assertEquals(400, $response->getStatusCode());
        }
    }

    public function testInnovateServiceRedirectUrlWithWrongFormData()
    {
        $this->card = new Card($this->redirectUrlCardInfo['number'], $this->redirectUrlCardInfo['cvv'], new DateTime('2014-5'));
        $response   = $this->client->performPayment($this->transaction, $this->card, $this->billing, $this->browser);
        $this->assertInstanceOf('Namshi\Innovate\Http\Response\Redirect', $response);

        if ($response instanceof Redirect) {
            $mpiFinal = array(
                'PaRes'     => 'wrong data',
                'session'   => 'wrong session',
            );
            $response = $this->client->authorizeRemoteRequest($mpiFinal);

            $this->assertEquals(400, $response->getStatusCode());
        }
    }

    protected function getAcsForm(Redirect $response)
    {
        return array(
            'PaReq'     => $response->getPaReq(),
            'MD'        => $response->getSession(),
            'TermUrl'   => 'http:://google.com'
        );
    }
}
