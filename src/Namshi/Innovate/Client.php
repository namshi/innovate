<?php

namespace Namshi\Innovate;

use Guzzle\Service\Client as BaseClient;
use Namshi\Innovate\Request\Factory as RequestFactory;
use Namshi\Innovate\Payment\Transaction;

/**
 * HTTP client tied to the Innovate API.
 */
class Client extends BaseClient
{
    const INNOVATE_URL = "https://secure.innovatepayments.com/gateway/remote.xml";
    
    protected $storeId;
    protected $key;
    protected $transaction;
    
    /**
     * Constructor
     * 
     * @param type $storeId
     * @param type $key
     * @param \Namshi\Innovate\Payment\Transaction $transaction
     * @param string $baseUrl
     * @param array $config
     */
    public function __construct($storeId, $key, Transaction $transaction, $baseUrl = '', $config = null)
    {
        parent::__construct($baseUrl, $config);
        
        $this->setStoreId($storeId);
        $this->setKey($key);
        $this->setTransaction($transaction);
        $this->setRequestFactory(RequestFactory::getInstance());
    }
    
    /**
     * Sends a request to the Innovate API with all the informations about the
     * payment to be performed.
     * 
     * @return Request
     */
    public function performPayment()
    {        
        return $this->send($this->createRequest('POST', self::INNOVATE_URL, null));
    }
    
    /**
     * Creates a request, generating the XML body that is needed by Innovate.
     * 
     * @param string $method
     * @param string $uri
     * @param array $headers
     * @param string $body
     * @param array $options
     * @return Request
     */
    public function createRequest($method = 'GET', $uri = null, $headers = null, $body = null, array $options = array())
    {
        $request = parent::createRequest($method, $uri, $headers, $body);
        $request->createBody($this->getStoreId(), $this->getKey(), $this->getTransaction());

        return $request;
    }

    public function getStoreId()
    {
        return $this->storeId;
    }

    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function setKey($key)
    {
        $this->key = $key;
    }
    
    public function getTransaction()
    {
        return $this->transaction;
    }

    public function setTransaction(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }
}