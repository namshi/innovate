<?php

namespace Namshi\Innovate\Http\Message;

use Guzzle\Http\Message\EntityEnclosingRequest as BaseRequest;
use Namshi\Innovate\Payment\Transaction;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Namshi\Innovate\Payment\Card;
use Namshi\Innovate\Payment\BillingInformation;
use Namshi\Innovate\Payment\Browser;

class EntityEnclosingRequest extends BaseRequest
{
    protected $xmlBody;
    
    public function __construct($method, $url, $headers = array())
    {
        parent::__construct($method, $url, $headers);
        
        $this->xmlBody = array();
    }
    
    public function createBody($storeId, $key, Transaction $transaction, Card $card, BillingInformation $billing, Browser $browser, $mpiData)
    {
        $this->xmlBody = array();
        $this->addStoreId($storeId);
        $this->addKey($key);
        $this->addTransaction($transaction);
        $this->addCard($card);
        $this->addBilling($billing);
        $this->addBrowser($browser);

        if (!empty($mpiData)) {
            $this->addMpiData($mpiData);
        }
        
        $encoder    = new XmlEncoder('remote');
        $serializer = new Serializer(array(new GetSetMethodNormalizer()), array($encoder));
        $this->setBody($serializer->serialize($this->xmlBody, 'xml'));
    }

    public function createMpiBody($storeId, $key, Transaction $transaction, Card $card)
    {
        $this->xmlBody  = array();
        $this->addStoreId($storeId);
        $this->addKey($key);
        $this->addTransaction($transaction);
        $this->addCard($card);
        $encoder        = new XmlEncoder('remote');
        $serializer     = new Serializer(array(new GetSetMethodNormalizer()), array($encoder));
        $this->setBody($serializer->serialize($this->xmlBody, 'xml'));
    }

    protected function addStoreId($id)
    {
        $this->xmlBody['store'] = $id;
    }
    
    protected function addKey($key)
    {
        $this->xmlBody['key'] = $key;
    }

    public function addMpiData($mpiData)
    {
        $this->xmlBody['mpi'] = $mpiData;
    }
    
    protected function addTransaction(Transaction $transaction)
    {
        $this->xmlBody['tran'] = $transaction->toArray();
    }

    protected function addCard(Card $card)
    {
        $this->xmlBody['card'] = $card->toArray();
    }

    protected function addBilling(BillingInformation $card)
    {
        $this->xmlBody['billing'] = $card->toArray();
    }

    protected function addBrowser(Browser $browser)
    {
        $this->xmlBody['browser'] = $browser->toArray();
    }


}