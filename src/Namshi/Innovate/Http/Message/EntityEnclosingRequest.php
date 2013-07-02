<?php

namespace Namshi\Innovate\Http\Message;

use Guzzle\Http\Message\EntityEnclosingRequest as BaseRequest;
use SimpleXMLElement;
use Namshi\Innovate\Payment\Transaction;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

class EntityEnclosingRequest extends BaseRequest
{
    protected $xmlBody;
    
    public function __construct($method, $url, $headers = array())
    {
        parent::__construct($method, $url, $headers);
        
        $this->xmlBody = array();
    }
    
    public function createBody($storeId, $key, Transaction $transaction)
    {
        $this->addStoreId($storeId);
        $this->addKey($key);
        $this->addTransaction($transaction);
        $encoder    = new XmlEncoder('remote');
        $serializer = new Serializer(array(new GetSetMethodNormalizer()), array($encoder));
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
    
    protected function addTransaction(Transaction $transaction)
    {
        $this->xmlBody['tran'] = $transaction->toArray();
    }
}